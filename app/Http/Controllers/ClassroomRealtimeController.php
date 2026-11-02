<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ClassroomMessage;
use App\Models\ClassroomParticipant;
use App\Models\ClassroomRoomControl;
use App\Models\ClassroomSignal;
use App\Models\GroupActivity;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClassroomRealtimeController extends Controller
{
    /**
     * المزامنة الفورية لنبضات الحضور، والرسائل، وإشارات WebRTC الصوتية والفيديو
     */
    public function sync(Request $request, string $roomId)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $peerId = $request->input('peer_id') ?: ('peer_'.$user->id.'_'.substr(md5(session()->getId()), 0, 6));

        // التحقق من صلاحيات المعلم / الإداري
        $control = ClassroomRoomControl::getControlForRoom($roomId);
        $kickedUsers = $control->kicked_user_ids ?? [];
        $isKicked = in_array($user->id, $kickedUsers);

        // تنظيف الإشارات القديمة التي مر عليها أكثر من 20 ثانية
        ClassroomSignal::where('created_at', '<', now()->subSeconds(20))->delete();

        // فحص هل المستخدم كادر تعليمي / إداري
        $isStaff = in_array($user->role, ['admin', 'teacher', 'supervisor']);

        // جلب أو إنشاء سجل الحضور الفعلي في الغرفة مع تحديد حالة القبول (غرفة الانتظار)
        $participant = ClassroomParticipant::where('room_id', $roomId)->where('user_id', $user->id)->first();
        if (! $participant) {
            $initialAdmission = $isStaff ? 'admitted' : ($control->requires_admission ? 'pending' : 'admitted');
            $participant = ClassroomParticipant::create([
                'room_id' => $roomId,
                'user_id' => $user->id,
                'peer_id' => $peerId,
                'role' => $user->role,
                'has_camera' => $request->boolean('has_camera', false),
                'has_mic' => $request->boolean('has_mic', true),
                'is_hand_raised' => $request->boolean('is_hand_raised', false),
                'admission_status' => $initialAdmission,
                'last_seen_at' => now(),
            ]);
        } else {
            // إذا كان المستخدم كادراً، حالته مقبولة دائماً
            $admStatus = $isStaff ? 'admitted' : ($participant->admission_status ?: 'admitted');
            // إذا أرسل المستخدم رغبة في إعادة طلب الانضمام
            if ($request->input('request_admission') === '1' && $admStatus === 'rejected') {
                $admStatus = 'pending';
            }

            $participant->update([
                'peer_id' => $peerId,
                'role' => $user->role,
                'has_camera' => $request->boolean('has_camera', false),
                'has_mic' => $request->boolean('has_mic', true),
                'is_hand_raised' => $request->has('is_hand_raised') ? $request->boolean('is_hand_raised') : $participant->is_hand_raised,
                'admission_status' => $admStatus,
                'last_seen_at' => now(),
            ]);
        }

        // جلب قائمة الحاضرين المقبولين المتصلين فعلياً الآن (آخر 8 ثوانٍ)
        $activeParticipants = ClassroomParticipant::with('user')
            ->where('room_id', $roomId)
            ->where('admission_status', 'admitted')
            ->where('last_seen_at', '>=', now()->subSeconds(8))
            ->get()
            ->map(function ($p) {
                return [
                    'user_id' => $p->user_id,
                    'peer_id' => $p->peer_id,
                    'name' => $p->user?->name ?? 'مستخدم',
                    'role' => $p->user?->role ?? $p->role,
                    'student_code' => $p->user?->student_code ?? $p->user?->user_code,
                    'has_camera' => (bool) $p->has_camera,
                    'has_mic' => (bool) $p->has_mic,
                    'is_hand_raised' => (bool) $p->is_hand_raised,
                    'is_muted_by_host' => (bool) $p->is_muted_by_host,
                    'is_staff' => in_array($p->user?->role ?? $p->role, ['admin', 'teacher', 'supervisor']),
                ];
            });

        // قائمة طلبات الانضمام المعلقة بغرفة الانتظار (للمعلم والمشرف فقط)
        $pendingRequests = [];
        if ($isStaff) {
            $pendingRequests = ClassroomParticipant::with('user')
                ->where('room_id', $roomId)
                ->where('admission_status', 'pending')
                ->where('last_seen_at', '>=', now()->subSeconds(15))
                ->get()
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'user_id' => $p->user_id,
                        'peer_id' => $p->peer_id,
                        'name' => $p->user?->name ?? 'طالب',
                        'student_code' => $p->user?->student_code ?? $p->user?->user_code,
                        'role' => $p->role,
                        'requested_at' => $p->updated_at?->diffForHumans() ?? 'الآن',
                    ];
                });
        }

        // جلب إشارات WebRTC الصوتية والفيديو الموجهة لهذا النظير (Peer) - فقط للمقبولين
        $signals = [];
        if ($participant->admission_status === 'admitted') {
            $signals = ClassroomSignal::where('room_id', $roomId)
                ->where('to_peer_id', $peerId)
                ->orderBy('id')
                ->get()
                ->map(function ($sig) {
                    return [
                        'id' => $sig->id,
                        'from_peer_id' => $sig->from_peer_id,
                        'signal_type' => $sig->signal_type,
                        'payload' => json_decode($sig->payload, true) ?: $sig->payload,
                    ];
                });

            if ($signals->isNotEmpty()) {
                ClassroomSignal::whereIn('id', $signals->pluck('id'))->delete();
            }
        }

        // جلب الرسائل والتسجيلات الجديدة منذ آخر رسالة
        $lastMessageId = (int) $request->input('last_message_id', 0);
        $messages = [];
        if ($participant->admission_status === 'admitted') {
            $messages = ClassroomMessage::with('user')
                ->where('room_id', $roomId)
                ->where('type', '!=', 'system')
                ->where('id', '>', $lastMessageId)
                ->orderBy('id')
                ->get()
                ->map(function ($msg) {
                    return [
                        'id' => $msg->id,
                        'user_id' => $msg->user_id,
                        'user_name' => $msg->user?->name ?? 'مشارك',
                        'is_student' => $msg->user ? $msg->user->isStudent() : true,
                        'role' => $msg->user?->role ?? 'student',
                        'type' => $msg->type,
                        'content' => $msg->content,
                        'file_path' => $msg->file_path ? asset($msg->file_path) : null,
                        'file_name' => $msg->file_name,
                        'time' => $msg->created_at->format('H:i'),
                    ];
                });
        }

        $clientWhiteboardVersion = (int) $request->input('whiteboard_version', -1);
        $serverWhiteboardVersion = (int) ($control->whiteboard_version ?? 0);
        $whiteboardPayload = null;
        if ($clientWhiteboardVersion < $serverWhiteboardVersion) {
            $whiteboardPayload = json_decode($control->whiteboard_state, true) ?: $control->whiteboard_state;
        }

        return response()->json([
            'success' => true,
            'my_peer_id' => $peerId,
            'admission_status' => $participant->admission_status,
            'is_admitted' => ($participant->admission_status === 'admitted'),
            'participants' => $activeParticipants,
            'pending_requests' => $pendingRequests,
            'signals' => $signals,
            'messages' => $messages,
            'whiteboard_version' => $serverWhiteboardVersion,
            'whiteboard_state' => $whiteboardPayload,
            'laser_state' => $control->laser_state,
            'room_control' => [
                'policy' => $control->policy,
                'is_live' => (bool) $control->is_live,
                'requires_admission' => (bool) $control->requires_admission,
                'is_muted_by_host' => (bool) $participant->is_muted_by_host,
                'is_kicked' => $isKicked,
            ],
        ]);
    }

    /**
     * إرسال إشارة WebRTC (SDP Offer / Answer أو ICE Candidate)
     */
    public function sendSignal(Request $request, string $roomId)
    {
        $validated = $request->validate([
            'from_peer_id' => 'required|string',
            'to_peer_id' => 'required|string',
            'signal_type' => 'required|in:offer,answer,candidate',
            'payload' => 'required',
        ]);

        $payloadString = is_string($validated['payload'])
            ? $validated['payload']
            : json_encode($validated['payload']);

        ClassroomSignal::create([
            'room_id' => $roomId,
            'from_peer_id' => $validated['from_peer_id'],
            'to_peer_id' => $validated['to_peer_id'],
            'signal_type' => $validated['signal_type'],
            'payload' => $payloadString,
            'created_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * إرسال رسالة نصية، أو تسجيل صوتي، أو ورقة إجابة فورية في المحادثة
     */
    public function sendMessage(Request $request, string $roomId)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'type' => 'required|in:text,audio,image,worksheet',
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:524288', // 512MB
        ]);

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName() ?: ('upload_'.time().'.'.$file->guessExtension());
            $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');

            if ($validated['type'] === 'audio' && ($ext === 'bin' || empty($ext))) {
                $ext = 'webm';
            }

            $cleanName = time().'_'.Str::slug(pathinfo($originalName, PATHINFO_FILENAME) ?: 'file').'.'.$ext;
            $destination = public_path('uploads/classroom_chat');
            if (! file_exists($destination)) {
                @mkdir($destination, 0755, true);
            }
            $file->move($destination, $cleanName);
            $filePath = 'uploads/classroom_chat/'.$cleanName;
            $fileName = $originalName;
        }

        $msg = ClassroomMessage::create([
            'room_id' => $roomId,
            'user_id' => $user->id,
            'type' => $validated['type'],
            'content' => $validated['content'] ?? ($fileName ? "مرفق: {$fileName}" : null),
            'file_path' => $filePath,
            'file_name' => $fileName,
        ]);

        // إذا كانت الغرفة خاصة بمجموعة دراسية، نحفظ نسخة في حائط المجموعة كنشاط أو تسليم دائم
        $group = StudyGroup::where('room_id', $roomId)->first();
        if ($group) {
            $actType = match ($validated['type']) {
                'audio' => 'audio',
                'image', 'worksheet' => 'worksheet',
                default => 'announcement',
            };
            GroupActivity::create([
                'study_group_id' => $group->id,
                'user_id' => $user->id,
                'title' => $msg->content ?: ('مشاركة من '.$user->name),
                'type' => $actType,
                'file_path' => $filePath,
                'file_name' => $fileName,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'is_student' => $user->isStudent(),
                'role' => $user->role,
                'type' => $msg->type,
                'content' => $msg->content,
                'file_path' => $msg->file_path ? asset($msg->file_path) : null,
                'file_name' => $msg->file_name,
                'time' => $msg->created_at->format('H:i'),
            ],
        ]);
    }

    /**
     * رفع أو خفض يد الطالب لطلب الكلمة
     */
    public function toggleHand(Request $request, string $roomId)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $participant = ClassroomParticipant::firstOrCreate(
            ['room_id' => $roomId, 'user_id' => $user->id],
            [
                'peer_id' => 'peer_'.$user->id.'_'.substr(md5(session()->getId()), 0, 6),
                'role' => $user->role,
                'last_seen_at' => now(),
            ]
        );

        $isRaised = ! $participant->is_hand_raised;
        $participant->is_hand_raised = $isRaised;
        $participant->last_seen_at = now();
        $participant->save();

        return response()->json([
            'success' => true,
            'is_hand_raised' => $isRaised,
        ]);
    }

    /**
     * أدوات التحكم الإدارية للأستاذ والمشرف (كتم المايك، السماح بالكلام، الطرد، تبديل سياسة الغرفة)
     */
    public function updateControls(Request $request, string $roomId)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // التحقق هل المستخدم صاحب صلاحية إدارية في هذه الغرفة
        $isAuthorized = ($user->isAdmin() || $user->isSupervisor() || $user->isTeacher());
        if (! $isAuthorized) {
            $group = StudyGroup::with(['teachers', 'supervisors'])->where('room_id', $roomId)->first();
            if ($group) {
                $isAuthorized = ($user->id === $group->teacher_id
                    || $user->id === $group->supervisor_id
                    || $group->teachers->contains('id', $user->id)
                    || $group->supervisors->contains('id', $user->id));
            }
            $booking = Booking::where('room_id', $roomId)->first();
            if ($booking && $user->id === $booking->teacher_id) {
                $isAuthorized = true;
            }
        }

        if (! $isAuthorized) {
            return response()->json(['error' => 'غير مصرح لك بإدارة عناصر التحكم في هذه الغرفة.'], 403);
        }

        $action = $request->input('action');
        $control = ClassroomRoomControl::getControlForRoom($roomId);

        if ($action === 'toggle_policy') {
            $newPolicy = $control->policy === 'open' ? 'permission' : 'open';
            $control->policy = $newPolicy;
            $control->save();

            return response()->json(['success' => true, 'policy' => $newPolicy]);
        }

        if ($action === 'mute_user') {
            $targetUserId = (int) $request->input('user_id');
            $p = ClassroomParticipant::where('room_id', $roomId)->where('user_id', $targetUserId)->first();
            if ($p) {
                $p->is_muted_by_host = true;
                $p->save();
            }

            return response()->json(['success' => true, 'muted' => true]);
        }

        if ($action === 'allow_speak') {
            $targetUserId = (int) $request->input('user_id');
            $p = ClassroomParticipant::where('room_id', $roomId)->where('user_id', $targetUserId)->first();
            if ($p) {
                $p->is_muted_by_host = false;
                $p->is_hand_raised = false;
                $p->save();
            }

            return response()->json(['success' => true, 'allowed' => true]);
        }

        if ($action === 'lower_hand') {
            $targetUserId = (int) $request->input('user_id');
            $p = ClassroomParticipant::where('room_id', $roomId)->where('user_id', $targetUserId)->first();
            if ($p) {
                $p->is_hand_raised = false;
                $p->save();
            }

            return response()->json(['success' => true]);
        }

        if ($action === 'kick_user') {
            $targetUserId = (int) $request->input('user_id');
            $kicked = $control->kicked_user_ids ?? [];
            if (! in_array($targetUserId, $kicked)) {
                $kicked[] = $targetUserId;
                $control->kicked_user_ids = $kicked;
                $control->save();
            }

            $p = ClassroomParticipant::where('room_id', $roomId)->where('user_id', $targetUserId)->first();
            $p?->delete();

            return response()->json(['success' => true, 'kicked' => true]);
        }

        if ($action === 'admit_student') {
            $targetUserId = (int) $request->input('user_id');
            $p = ClassroomParticipant::where('room_id', $roomId)->where('user_id', $targetUserId)->first();
            if ($p) {
                $p->admission_status = 'admitted';
                $p->save();
            }

            return response()->json(['success' => true, 'admitted' => true]);
        }

        if ($action === 'admit_all') {
            $pendingCount = ClassroomParticipant::where('room_id', $roomId)
                ->where('admission_status', 'pending')
                ->count();

            if ($pendingCount > 0) {
                ClassroomParticipant::where('room_id', $roomId)
                    ->where('admission_status', 'pending')
                    ->update(['admission_status' => 'admitted']);
            }

            return response()->json(['success' => true, 'admitted_all' => true]);
        }

        if ($action === 'reject_student') {
            $targetUserId = (int) $request->input('user_id');
            $p = ClassroomParticipant::where('room_id', $roomId)->where('user_id', $targetUserId)->first();
            if ($p) {
                $p->admission_status = 'rejected';
                $p->save();
            }

            return response()->json(['success' => true, 'rejected' => true]);
        }

        if ($action === 'clear_chat') {
            ClassroomMessage::where('room_id', $roomId)->delete();

            return response()->json(['success' => true, 'cleared' => true]);
        }

        if ($action === 'mute_all') {
            ClassroomParticipant::where('room_id', $roomId)->update(['is_muted_by_host' => true]);
            $control->policy = 'moderated';
            $control->save();

            return response()->json(['success' => true, 'muted_all' => true]);
        }

        if ($action === 'refresh_all') {
            return response()->json(['success' => true, 'refreshed' => true]);
        }

        if ($action === 'toggle_requires_admission') {
            $control->requires_admission = ! $control->requires_admission;
            $control->save();

            return response()->json(['success' => true, 'requires_admission' => $control->requires_admission]);
        }

        return response()->json(['error' => 'إجراء غير معروف'], 400);
    }

    /**
     * تحديث السبورة ومساحة الشرح التفاعلية (Zoom Live Workspace)
     */
    public function updateWhiteboard(Request $request, string $roomId)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $isStaff = in_array($user->role, ['admin', 'teacher', 'supervisor']);
        $control = ClassroomRoomControl::getControlForRoom($roomId);

        // السماح للمعلم والمشرف والإدارة بالتحكم بالسبورة وشرح الدروس
        if (! $isStaff) {
            return response()->json(['error' => 'فقط المعلم ومسؤول الجلسة يمكنهم التحكم بمساحة الشرح والسبورة'], 403);
        }

        $action = $request->input('action');

        if ($action === 'laser_move') {
            $control->laser_state = [
                'x' => (float) $request->input('x', 0),
                'y' => (float) $request->input('y', 0),
                'active' => (bool) $request->input('active', true),
                'user_name' => $user->name,
                'time' => microtime(true),
            ];
            $control->save();

            return response()->json(['success' => true]);
        }

        if ($action === 'laser_hide') {
            $control->laser_state = ['active' => false];
            $control->save();

            return response()->json(['success' => true]);
        }

        if ($action === 'save_state') {
            $state = $request->input('state');
            $control->whiteboard_state = is_string($state) ? $state : json_encode($state);
            $control->whiteboard_version = ($control->whiteboard_version ?? 0) + 1;
            $control->save();

            return response()->json([
                'success' => true,
                'whiteboard_version' => $control->whiteboard_version,
            ]);
        }

        if ($action === 'clear') {
            $existing = json_decode($control->whiteboard_state, true) ?: [];
            $existing['strokes'] = [];
            $control->whiteboard_state = json_encode($existing);
            $control->whiteboard_version = ($control->whiteboard_version ?? 0) + 1;
            $control->save();

            return response()->json([
                'success' => true,
                'whiteboard_version' => $control->whiteboard_version,
            ]);
        }

        if ($action === 'set_background') {
            $existing = json_decode($control->whiteboard_state, true) ?: [];
            $existing['bg_type'] = $request->input('bg_type', 'image'); // 'whiteboard' | 'blackboard' | 'grid' | 'image' | 'pdf'
            $existing['bg_url'] = $request->input('bg_url');
            $existing['bg_title'] = $request->input('bg_title', '');
            $existing['page_num'] = (int) $request->input('page_num', 1);
            $existing['strokes'] = [];
            $control->whiteboard_state = json_encode($existing);
            $control->whiteboard_version = ($control->whiteboard_version ?? 0) + 1;
            $control->save();

            return response()->json([
                'success' => true,
                'whiteboard_version' => $control->whiteboard_version,
            ]);
        }

        if ($action === 'upload_bg_file' && $request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png');
            $originalName = $file->getClientOriginalName() ?: ('whiteboard_bg_'.time().'.'.$ext);
            $rawBase = pathinfo($originalName, PATHINFO_FILENAME);
            $slug = Str::slug($rawBase);
            if (empty($slug)) {
                $slug = 'file_'.substr(md5($originalName.time()), 0, 8);
            }
            $cleanName = time().'_'.$slug.'.'.$ext;

            $dest = public_path('uploads/classroom_whiteboard');
            if (! file_exists($dest)) {
                @mkdir($dest, 0755, true);
            }
            $file->move($dest, $cleanName);
            $url = '/uploads/classroom_whiteboard/'.$cleanName;

            $isPdf = ($ext === 'pdf');
            $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'm4v', 'avi', 'mkv', 'flv']);
            $bgType = $isPdf ? 'pdf' : ($isVideo ? 'video' : 'image');

            $existing = json_decode($control->whiteboard_state, true) ?: [];
            $existing['bg_type'] = $bgType;
            $existing['bg_url'] = $url;
            $existing['bg_title'] = $originalName;
            $existing['page_num'] = 1;
            $existing['strokes'] = [];
            $control->whiteboard_state = json_encode($existing);
            $control->whiteboard_version = ($control->whiteboard_version ?? 0) + 1;
            $control->save();

            return response()->json([
                'success' => true,
                'url' => $url,
                'title' => $originalName,
                'bg_type' => $existing['bg_type'],
                'whiteboard_version' => $control->whiteboard_version,
            ]);
        }

        return response()->json(['error' => 'إجراء غير معروف للسبورة'], 400);
    }
}
