<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ClassroomMessage;
use App\Models\ClassroomParticipant;
use App\Models\ClassroomRoomControl;
use App\Models\ClassroomSignal;
use App\Models\GroupActivity;
use App\Models\GroupSession;
use App\Models\GroupStudentEvaluation;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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

        // تنظيف الإشارات القديمة دورياً بدون إثقال قاعدة البيانات بكل طلب
        if (random_int(1, 40) === 1) {
            ClassroomSignal::where('created_at', '<', now()->subSeconds(20))->delete();
        }

        // فحص هل المستخدم كادر تعليمي / إداري أو عضو رسمي مسجل بالمجموعة/الحجز مع كاش لمدة 60 ثانية
        $isStaff = in_array($user->role, ['admin', 'teacher', 'supervisor']);
        $isAuthorizedMember = Cache::remember("room_auth_{$roomId}_{$user->id}", 60, function () use ($user, $roomId) {
            $group = StudyGroup::with('students')->where('room_id', $roomId)->first();
            if ($group) {
                if ($group->students->contains('id', $user->id)
                    || $user->id === $group->teacher_id
                    || $user->id === $group->supervisor_id) {
                    return true;
                }
            }
            $booking = Booking::where('room_id', $roomId)->first();
            if ($booking) {
                if ($user->id === $booking->student_id || $user->id === $booking->teacher_id) {
                    return true;
                }
            }

            return false;
        });

        $canEnterDirectly = $isStaff || $isAuthorizedMember || ! $control->requires_admission;

        // جلب أو إنشاء سجل الحضور الفعلي في الغرفة مع تحديد حالة القبول (غرفة الانتظار)
        $participant = ClassroomParticipant::where('room_id', $roomId)->where('user_id', $user->id)->first();
        if (! $participant) {
            $initialAdmission = $canEnterDirectly ? 'admitted' : 'pending';
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
            Cache::forget("active_participants_{$roomId}");
        } else {
            // إذا كان المستخدم كادراً أو عضواً مصرحاً، حالته مقبولة دائماً
            $admStatus = $canEnterDirectly ? 'admitted' : ($participant->admission_status ?: 'admitted');
            // إذا أرسل المستخدم رغبة في إعادة طلب الانضمام
            if ($request->input('request_admission') === '1' && $admStatus === 'rejected') {
                $admStatus = 'pending';
            }

            $hasCam = $request->boolean('has_camera', false);
            $hasMic = $request->boolean('has_mic', true);
            $isHandRaised = $request->has('is_hand_raised') ? $request->boolean('is_hand_raised') : $participant->is_hand_raised;

            // تحديث قاعدة البيانات فقط عند تغير الحالة أو بعد مرور 8 ثوانٍ على الأقل لتسريع الاستجابة بنسبة 90%
            $stateChanged = ($participant->peer_id !== $peerId)
                || ((bool) $participant->has_camera !== $hasCam)
                || ((bool) $participant->has_mic !== $hasMic)
                || ((bool) $participant->is_hand_raised !== (bool) $isHandRaised)
                || ($participant->admission_status !== $admStatus);

            $heartbeatExpired = ! $participant->last_seen_at || $participant->last_seen_at->lt(now()->subSeconds(8));

            if ($stateChanged || $heartbeatExpired) {
                $participant->update([
                    'peer_id' => $peerId,
                    'role' => $user->role,
                    'has_camera' => $hasCam,
                    'has_mic' => $hasMic,
                    'is_hand_raised' => $isHandRaised,
                    'admission_status' => $admStatus,
                    'last_seen_at' => now(),
                ]);
                if ($stateChanged) {
                    Cache::forget("active_participants_{$roomId}");
                }
            }
        }

        // جلب قائمة الحاضرين المقبولين المتصلين فعلياً الآن مع كاش خفيف لمدة ثانيتين
        $activeParticipants = Cache::remember("active_participants_{$roomId}", 2, function () use ($roomId) {
            return ClassroomParticipant::with('user')
                ->where('room_id', $roomId)
                ->where('admission_status', 'admitted')
                ->where('last_seen_at', '>=', now()->subSeconds(12))
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
                })->values()->all();
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

        // جلب إشارات WebRTC الصوتية والفيديو الموجهة لهذا النظير (Peer) - فقط للمقبولين (مع دعم كاش الإشارات فائق السرعة)
        $signals = [];
        if ($participant->admission_status === 'admitted') {
            $cacheKey = "signals_{$roomId}_{$peerId}";
            $cachedSignals = Cache::pull($cacheKey, []);

            if (! empty($cachedSignals)) {
                $signals = $cachedSignals;
                ClassroomSignal::where('room_id', $roomId)->where('to_peer_id', $peerId)->delete();
            } else {
                $dbSignals = ClassroomSignal::where('room_id', $roomId)
                    ->where('to_peer_id', $peerId)
                    ->orderBy('id')
                    ->get();

                if ($dbSignals->isNotEmpty()) {
                    $signals = $dbSignals->map(function ($sig) {
                        return [
                            'id' => $sig->id,
                            'from_peer_id' => $sig->from_peer_id,
                            'signal_type' => $sig->signal_type,
                            'payload' => json_decode($sig->payload, true) ?: $sig->payload,
                        ];
                    })->all();
                    ClassroomSignal::whereIn('id', $dbSignals->pluck('id'))->delete();
                }
            }
        }

        // جلب الرسائل والتسجيلات الجديدة منذ آخر رسالة مع فحص سريع
        $lastMessageId = (int) $request->input('last_message_id', 0);
        $actualMaxId = (int) Cache::remember("classroom_max_msg_{$roomId}", 2, function () use ($roomId) {
            return ClassroomMessage::where('room_id', $roomId)->max('id') ?? 0;
        });
        $chatWasCleared = ($lastMessageId > 0 && $actualMaxId < $lastMessageId);

        $messages = [];
        if ($participant->admission_status === 'admitted' && $actualMaxId > $lastMessageId) {
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
        if ($clientWhiteboardVersion < $serverWhiteboardVersion || $clientWhiteboardVersion === -1) {
            $whiteboardPayload = json_decode($control->whiteboard_state, true) ?: $control->whiteboard_state;
            if (is_array($whiteboardPayload) && ! empty($whiteboardPayload['bg_url'])) {
                $bgUrl = $whiteboardPayload['bg_url'];
                $parsed = parse_url($bgUrl);
                $isInternalPath = ! empty($parsed['path']) && (str_starts_with($parsed['path'], '/resources/') || str_starts_with($parsed['path'], '/storage/') || str_starts_with($parsed['path'], '/classroom/'));
                $isLocalHost = empty($parsed['host']) || in_array($parsed['host'], ['localhost', '127.0.0.1']) || ! empty($parsed['port']);
                if ($isInternalPath && $isLocalHost) {
                    $whiteboardPayload['bg_url'] = url($parsed['path'].(! empty($parsed['query']) ? '?'.$parsed['query'] : ''));
                }
            }
        }

        $forceRefreshTime = Cache::get('classroom_force_refresh_'.$roomId);
        $clientSessionStart = (int) $request->input('session_start', 0);
        $forceRefresh = false;
        if ($forceRefreshTime && $clientSessionStart > 0 && $forceRefreshTime > $clientSessionStart) {
            $forceRefresh = true;
        }

        // مؤشر الليزر من الذاكرة اللحظية لتسريع الشرح
        $laserState = Cache::get("classroom_laser_{$roomId}", $control->laser_state);

        return response()->json([
            'success' => true,
            'my_peer_id' => $peerId,
            'admission_status' => $participant->admission_status,
            'is_admitted' => ($participant->admission_status === 'admitted'),
            'participants' => $activeParticipants,
            'pending_requests' => $pendingRequests,
            'signals' => $signals,
            'messages' => $messages,
            'chat_cleared' => $chatWasCleared,
            'force_refresh' => $forceRefresh,
            'whiteboard_version' => $serverWhiteboardVersion,
            'whiteboard_state' => $whiteboardPayload,
            'laser_state' => $laserState,
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
            'signal_type' => 'required|in:offer,answer,candidate,candidate_batch',
            'payload' => 'required',
        ]);

        $payload = is_string($validated['payload'])
            ? (json_decode($validated['payload'], true) ?: $validated['payload'])
            : $validated['payload'];

        $payloadString = is_string($validated['payload'])
            ? $validated['payload']
            : json_encode($validated['payload']);

        // وضع الإشارة في الكاش اللحظي لتسريع تسليمها بأقل من 30 جزء من الثانية
        $cacheKey = "signals_{$roomId}_{$validated['to_peer_id']}";
        $existing = Cache::get($cacheKey, []);
        $existing[] = [
            'id' => (string) Str::uuid(),
            'from_peer_id' => $validated['from_peer_id'],
            'signal_type' => $validated['signal_type'],
            'payload' => $payload,
        ];
        Cache::put($cacheKey, $existing, 30);

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
            Cache::forget("classroom_max_msg_{$roomId}");

            return response()->json(['success' => true, 'cleared' => true]);
        }

        if ($action === 'mute_all') {
            ClassroomParticipant::where('room_id', $roomId)->update(['is_muted_by_host' => true]);
            $control->policy = 'moderated';
            $control->save();
            Cache::forget("active_participants_{$roomId}");

            return response()->json(['success' => true, 'muted_all' => true]);
        }

        if ($action === 'refresh_all') {
            $control->whiteboard_version = ($control->whiteboard_version ?? 0) + 1;
            $control->save();
            Cache::put('classroom_force_refresh_'.$roomId, time(), 30);

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
            $laserState = [
                'x' => (float) $request->input('x', 0),
                'y' => (float) $request->input('y', 0),
                'active' => (bool) $request->input('active', true),
                'user_name' => $user->name,
                'time' => microtime(true),
            ];
            Cache::put("classroom_laser_{$roomId}", $laserState, 5);

            return response()->json(['success' => true]);
        }

        if ($action === 'laser_hide') {
            Cache::put("classroom_laser_{$roomId}", ['active' => false], 5);

            return response()->json(['success' => true]);
        }

        if ($action === 'save_state') {
            $state = $request->input('state');
            if (is_array($state) && ! empty($state['bg_url'])) {
                $parsed = parse_url($state['bg_url']);
                if (! empty($parsed['path']) && (str_starts_with($parsed['path'], '/resources/') || str_starts_with($parsed['path'], '/storage/') || str_starts_with($parsed['path'], '/classroom/'))) {
                    $state['bg_url'] = $parsed['path'].(! empty($parsed['query']) ? '?'.$parsed['query'] : '');
                }
            }
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
            $bgUrl = $request->input('bg_url');
            if ($bgUrl) {
                $parsed = parse_url($bgUrl);
                if (! empty($parsed['path']) && (str_starts_with($parsed['path'], '/resources/') || str_starts_with($parsed['path'], '/storage/') || str_starts_with($parsed['path'], '/classroom/'))) {
                    $bgUrl = $parsed['path'].(! empty($parsed['query']) ? '?'.$parsed['query'] : '');
                }
            }
            $existing['bg_url'] = $bgUrl;
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

    /**
     * بروتوكول تصفح الويب الآمن وتخطي قيود التضمين بالسبورة (Whiteboard Mini-Browser Proxy)
     */
    public function proxyBrowse(Request $request)
    {
        $url = $request->query('url');
        if (! $url) {
            return response('<!DOCTYPE html><html dir="rtl"><body style="font-family:sans-serif;text-align:center;padding:50px;"><h3>يرجى تحديد رابط للتصفح</h3></body></html>', 200, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $url = 'https://'.$url;
        }

        $parsed = parse_url($url);
        $host = strtolower($parsed['host'] ?? '');

        // حظر العناوين المحلية والشبكات الداخلية لحماية الخادم (SSRF Protection)
        if (empty($host) || in_array($host, ['localhost', '127.0.0.1', '::1', '0.0.0.0']) || str_ends_with($host, '.local') || str_ends_with($host, '.internal')) {
            return response('غير مسموح بتصفح هذا النطاق محلياً', 403, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        $ip = gethostbyname($host);
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return response('غير مسموح بالوصول إلى عناوين الشبكة الداخلية الخاصة', 403, ['Content-Type' => 'text/plain; charset=UTF-8']);
            }
        }

        // التحويل التلقائي لألعاب ومسابقات Wordwall إلى وضع التضمين التفاعلي الخفيف
        if (preg_match('#wordwall\.net/(?:[a-z]{2}-[a-z]{2}/)?(?:resource|play)/([0-9]+)#i', $url, $m)) {
            return redirect()->away('https://wordwall.net/embed/'.$m[1]);
        }

        // دعم Google Search والصفحة الرئيسية مباشرة في وضع التضمين الرسمي (&igu=1)
        if (str_contains($host, 'google.') && ! str_contains($url, 'igu=1')) {
            $sep = str_contains($url, '?') ? '&' : '?';

            return redirect()->away($url.$sep.'igu=1');
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(12)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'ar,en-US;q=0.9,en;q=0.8',
                ])
                ->get($url);

            $contentType = $response->header('Content-Type') ?: 'text/html; charset=UTF-8';

            if (! str_contains(strtolower($contentType), 'text/html')) {
                return response($response->body(), $response->status(), [
                    'Content-Type' => $contentType,
                    'Access-Control-Allow-Origin' => '*',
                ]);
            }

            $html = $response->body();
            $baseUrl = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '');
            if (isset($parsed['port'])) {
                $baseUrl .= ':'.$parsed['port'];
            }
            $basePath = isset($parsed['path']) ? dirname($parsed['path']) : '/';
            $fullBase = rtrim($baseUrl.$basePath, '/').'/';

            $baseTag = '<base href="'.htmlspecialchars($fullBase, ENT_QUOTES, 'UTF-8').'">';
            $proxyScript = <<<'JS'
<script>
document.addEventListener('click', function(e) {
    var a = e.target.closest('a');
    if (a && a.href && !a.href.startsWith('javascript:') && !a.href.startsWith('#')) {
        e.preventDefault();
        window.location.href = '/classroom/proxy-browse?url=' + encodeURIComponent(a.href);
    }
}, true);
</script>
JS;

            if (stripos($html, '<head>') !== false) {
                $html = preg_replace('#<head>#i', '<head>'.$baseTag.$proxyScript, $html, 1);
            } else {
                $html = $baseTag.$proxyScript.$html;
            }

            return response($html, $response->status(), [
                'Content-Type' => 'text/html; charset=UTF-8',
                'X-Frame-Options' => 'ALLOWALL',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                'Access-Control-Allow-Headers' => '*',
            ]);
        } catch (\Throwable $e) {
            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

            return response(<<<HTML
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head><meta charset="utf-8"><title>تصفح الموقع</title>
<style>
body{font-family:system-ui,-apple-system,sans-serif;background:#0f172a;color:#f8fafc;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;padding:20px;box-sizing:border-box;text-align:center;}
.card{background:#1e293b;border:1px solid #334155;border-radius:24px;padding:36px;max-width:500px;box-shadow:0 25px 30px -5px rgba(0,0,0,0.6);}
.btn-group{display:flex;flex-direction:column;gap:10px;margin-top:24px;}
a.btn, button.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;background:#4f46e5;color:#fff;text-decoration:none;padding:12px 20px;border-radius:14px;font-weight:bold;font-size:13px;border:none;cursor:pointer;transition:0.2s;}
a.btn:hover, button.btn:hover{background:#4338ca;}
button.btn-rose{background:#e11d48;}
button.btn-rose:hover{background:#be123c;}
button.btn-slate{background:#334155;}
button.btn-slate:hover{background:#475569;}
</style>
</head>
<body>
<div class="card">
    <div style="font-size:48px;margin-bottom:12px;">🌐</div>
    <h3 style="margin:0 0 10px 0;font-size:18px;font-weight:900;">الموقع يتطلب فتحه في تبويب مستقل</h3>
    <p style="font-size:13px;color:#94a3b8;line-height:1.6;margin-bottom:8px;">هذا الموقع يفرض حماية من قِبل إدارته تمنع تضمينه داخل الصفحات، يمكنك فتحه فوراً في متصفح كروم أو الرجوع للسبورة.</p>
    <div class="btn-group">
        <a href="{$safeUrl}" target="_blank" rel="noopener" class="btn">فتح الموقع في تبويب كروم جديد ↗️</a>
        <button type="button" onclick="if(window.parent&&window.parent.closeWbMediaLayer){window.parent.closeWbMediaLayer();}else{window.history.back();}" class="btn btn-rose">🔙 إنهاء التصفح والرجوع للسبورة</button>
        <button type="button" onclick="if(window.parent&&window.parent.navigateBrowserToGoogleHome){window.parent.navigateBrowserToGoogleHome();}" class="btn btn-slate">🔍 فتح محرك بحث جوجل</button>
    </div>
</div>
</body>
</html>
HTML
                , 200, ['Content-Type' => 'text/html; charset=UTF-8', 'X-Frame-Options' => 'ALLOWALL', 'Access-Control-Allow-Origin' => '*']);
        }
    }

    /**
     * منح مكافأة بنجمة لطالب محدد أو لجميع الحاضرين في الحصة وإضافتها لرصيد تقييمات الطالب
     */
    public function rewardStar(Request $request, string $roomId)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $isStaff = in_array($user->role, ['admin', 'teacher', 'supervisor']);
        $group = StudyGroup::with(['students', 'teachers', 'supervisors'])->where('room_id', $roomId)->first();
        $booking = Booking::with('student')->where('room_id', $roomId)->first();

        $isAuthorized = $isStaff;
        if (! $isAuthorized && $group) {
            $isAuthorized = ($user->id === $group->teacher_id
                || $user->id === $group->supervisor_id
                || $group->teachers->contains('id', $user->id)
                || $group->supervisors->contains('id', $user->id));
        }
        if (! $isAuthorized && $booking) {
            $isAuthorized = ($user->id === $booking->teacher_id);
        }

        if (! $isAuthorized) {
            return response()->json(['error' => 'عذراً، فقط المعلم ومسؤول الجلسة يمكنهم منح مكافآت النجوم.'], 403);
        }

        $studentId = $request->input('student_id');
        $amount = max(1, min(10, (int) $request->input('amount', 1)));
        $note = $request->input('note', 'مشاركة وتفاعل متميز في الحصة ⭐');

        if ($studentId === 'all') {
            // منح مكافأة لجميع الطلاب الحاضرين في الغرفة
            $attendeeIds = ClassroomParticipant::where('room_id', $roomId)
                ->where('role', 'student')
                ->where('admission_status', 'admitted')
                ->pluck('user_id');

            if ($attendeeIds->isEmpty() && $group) {
                $attendeeIds = $group->students->pluck('id');
            }

            $students = User::whereIn('id', $attendeeIds)->where('role', 'student')->get();
            $rewarded = [];

            foreach ($students as $st) {
                $st->increment('stars_balance', $amount);
                $rewarded[] = [
                    'id' => $st->id,
                    'name' => $st->name,
                    'new_balance' => $st->fresh()->stars_balance,
                ];

                if ($group) {
                    $activeSession = GroupSession::where('study_group_id', $group->id)
                        ->whereDate('session_date', now()->toDateString())
                        ->latest()
                        ->first();
                    if ($activeSession) {
                        $eval = GroupStudentEvaluation::firstOrCreate(
                            [
                                'group_session_id' => $activeSession->id,
                                'study_group_id' => $group->id,
                                'student_id' => $st->id,
                            ],
                            [
                                'evaluator_id' => $user->id,
                                'evaluator_name' => $user->name,
                                'evaluator_role' => $user->role,
                                'attendance_status' => 'present',
                            ]
                        );
                        $eval->increment('system_total_score', $amount * 2);
                        $eval->increment('participation_score', $amount);
                    }
                }
            }

            ClassroomMessage::create([
                'room_id' => $roomId,
                'user_id' => $user->id,
                'type' => 'announcement',
                'content' => "🌟 قام المعلم ({$user->name}) بمنح مكافأة ({$amount} ⭐) لجميع الطلاب الحاضرين تقديراً لتفاعلهم وإبداعهم! 👏",
            ]);

            return response()->json([
                'success' => true,
                'is_all' => true,
                'amount' => $amount,
                'students' => $rewarded,
                'message' => "تم منح {$amount} ⭐ لجميع الطلاب الحاضرين بنجاح!",
            ]);
        }

        // طالب محدد
        $targetStudentId = (int) $studentId;
        if (! $targetStudentId && $booking && $booking->student_id) {
            $targetStudentId = $booking->student_id;
        }

        $student = User::where('id', $targetStudentId)->where('role', 'student')->first();
        if (! $student) {
            return response()->json(['error' => 'الطالب المحدد غير موجود.'], 404);
        }

        $student->increment('stars_balance', $amount);
        $newBalance = $student->fresh()->stars_balance;

        if ($group) {
            $activeSession = GroupSession::where('study_group_id', $group->id)
                ->whereDate('session_date', now()->toDateString())
                ->latest()
                ->first();
            if ($activeSession) {
                $eval = GroupStudentEvaluation::firstOrCreate(
                    [
                        'group_session_id' => $activeSession->id,
                        'study_group_id' => $group->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'evaluator_id' => $user->id,
                        'evaluator_name' => $user->name,
                        'evaluator_role' => $user->role,
                        'attendance_status' => 'present',
                    ]
                );
                $eval->increment('system_total_score', $amount * 2);
                $eval->increment('participation_score', $amount);
            }
        }

        ClassroomMessage::create([
            'room_id' => $roomId,
            'user_id' => $user->id,
            'type' => 'announcement',
            'content' => "🌟 قام المعلم ({$user->name}) بمنح الطالب [{$student->name}] ({$amount} ⭐) - {$note} 👏",
        ]);

        return response()->json([
            'success' => true,
            'student_id' => $student->id,
            'student_name' => $student->name,
            'amount' => $amount,
            'new_balance' => $newBalance,
            'message' => "تم منح {$amount} ⭐ للطالب ({$student->name}) بنجاح! رصيده الحالي: {$newBalance} ⭐",
        ]);
    }
}
