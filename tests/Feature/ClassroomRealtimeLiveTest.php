<?php

namespace Tests\Feature;

use App\Models\ClassroomMessage;
use App\Models\ClassroomParticipant;
use App\Models\ClassroomRoomControl;
use App\Models\ClassroomSignal;
use App\Models\Course;
use App\Models\Level;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClassroomRealtimeLiveTest extends TestCase
{
    protected function getAdmin(): User
    {
        return User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    protected function getStudent(): User
    {
        return User::where('role', 'student')->first() ?? User::create([
            'name' => 'طالب تجريبي',
            'email' => 'student_wb_'.rand(100, 999).'@test.com',
            'phone_whatsapp' => '96773'.rand(1000000, 9999999),
            'password' => Hash::make('Secret123!'),
            'role' => 'student',
            'status' => 'active',
            'is_active' => true,
        ]);
    }

    public function test_classroom_sync_registers_participant_and_returns_realtime_state(): void
    {
        $admin = $this->getAdmin();
        $roomId = 'test-room-sync-'.rand(100, 999);

        $response = $this->actingAs($admin)->postJson(route('classroom.sync', $roomId), [
            'peer_id' => 'peer_admin_test',
            'has_camera' => true,
            'has_mic' => true,
            'is_hand_raised' => false,
            'last_message_id' => 0,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'my_peer_id' => 'peer_admin_test',
        ]);
        $response->assertJsonStructure([
            'participants',
            'signals',
            'messages',
            'room_control' => [
                'policy',
                'is_live',
                'is_muted_by_host',
                'is_kicked',
            ],
        ]);

        $this->assertDatabaseHas('classroom_participants', [
            'room_id' => $roomId,
            'user_id' => $admin->id,
            'peer_id' => 'peer_admin_test',
            'has_camera' => 1,
            'has_mic' => 1,
        ]);
    }

    public function test_classroom_messages_can_be_sent_and_synced(): void
    {
        $student = User::create([
            'name' => 'طالب محادثة حية',
            'email' => null,
            'phone_whatsapp' => '96773'.rand(1000000, 9999999),
            'password' => Hash::make('Secret123!'),
            'role' => 'student',
            'status' => 'active',
            'is_active' => true,
        ]);

        $group = StudyGroup::create([
            'name' => 'مجموعة بث مباشر '.rand(100, 999),
            'course_id' => Course::first()?->id,
            'level_id' => Level::first()?->id,
            'status' => 'active',
        ]);

        $roomId = $group->room_id;

        // إرسال رسالة نصية
        $textRes = $this->actingAs($student)->postJson(route('classroom.message', $roomId), [
            'type' => 'text',
            'content' => 'السلام عليكم يا أستاذ، عندي سؤال حول درس اليوم.',
        ]);

        $textRes->assertOk();
        $textRes->assertJson([
            'success' => true,
            'message' => [
                'type' => 'text',
                'content' => 'السلام عليكم يا أستاذ، عندي سؤال حول درس اليوم.',
            ],
        ]);

        // إرسال تسجيل صوتي
        $audioFile = UploadedFile::fake()->create('voice_note.webm', 80, 'audio/webm');
        $voiceRes = $this->actingAs($student)->postJson(route('classroom.message', $roomId), [
            'type' => 'audio',
            'content' => 'تسجيل صوتي',
            'attachment' => $audioFile,
        ]);

        $voiceRes->assertOk();
        $voiceRes->assertJson([
            'success' => true,
            'message' => [
                'type' => 'audio',
            ],
        ]);

        // التحقق من مزامنة الرسائل عبر sync endpoint
        $admin = $this->getAdmin();
        $syncRes = $this->actingAs($admin)->postJson(route('classroom.sync', $roomId), [
            'peer_id' => 'peer_admin_check',
            'last_message_id' => 0,
        ]);

        $syncRes->assertOk();
        $this->assertCount(2, $syncRes->json('messages'));

        // تنظيف
        ClassroomMessage::where('room_id', $roomId)->delete();
        ClassroomParticipant::where('room_id', $roomId)->delete();
        $group->delete();
        $student->delete();
    }

    public function test_webrtc_signaling_exchange(): void
    {
        $admin = $this->getAdmin();
        $roomId = 'test-room-signal-'.rand(100, 999);

        $signalPayload = ['sdp' => 'v=0...', 'type' => 'offer'];

        $postSignal = $this->actingAs($admin)->postJson(route('classroom.signal', $roomId), [
            'from_peer_id' => 'peer_1',
            'to_peer_id' => 'peer_2',
            'signal_type' => 'offer',
            'payload' => $signalPayload,
        ]);

        $postSignal->assertOk();

        $this->assertDatabaseHas('classroom_signals', [
            'room_id' => $roomId,
            'from_peer_id' => 'peer_1',
            'to_peer_id' => 'peer_2',
            'signal_type' => 'offer',
        ]);

        // عند قيام peer_2 بالمزامنة، يجب أن يستلم الإشارة وتُحذف
        $syncRes = $this->actingAs($admin)->postJson(route('classroom.sync', $roomId), [
            'peer_id' => 'peer_2',
        ]);

        $syncRes->assertOk();
        $this->assertCount(1, $syncRes->json('signals'));
        $this->assertEquals('peer_1', $syncRes->json('signals.0.from_peer_id'));

        // التحقق من حذفها حتى لا تتكرر
        $this->assertEquals(0, ClassroomSignal::where('room_id', $roomId)->where('to_peer_id', 'peer_2')->count());
    }

    public function test_student_hand_raise_and_teacher_moderator_controls(): void
    {
        $admin = $this->getAdmin();

        $student = User::create([
            'name' => 'طالب تجربة رفع يد',
            'email' => null,
            'phone_whatsapp' => '96773'.rand(1000000, 9999999),
            'password' => Hash::make('Secret123!'),
            'role' => 'student',
            'status' => 'active',
            'is_active' => true,
        ]);

        $roomId = 'test-room-moderator-'.rand(100, 999);

        // 1. الطالب يرفع يده
        $handRes = $this->actingAs($student)->postJson(route('classroom.hand', $roomId));
        $handRes->assertOk();
        $handRes->assertJson(['is_hand_raised' => true]);

        $p = ClassroomParticipant::where('room_id', $roomId)->where('user_id', $student->id)->first();
        $this->assertTrue((bool) $p->is_hand_raised);

        // 2. المعلم يغير سياسة الفصل إلى "بإذن الأستاذ فقط"
        $policyRes = $this->actingAs($admin)->postJson(route('classroom.controls', $roomId), [
            'action' => 'toggle_policy',
        ]);
        $policyRes->assertOk();
        $policyRes->assertJson(['policy' => 'permission']);

        // 3. المعلم يكتم صوت الطالب
        $muteRes = $this->actingAs($admin)->postJson(route('classroom.controls', $roomId), [
            'action' => 'mute_user',
            'user_id' => $student->id,
        ]);
        $muteRes->assertOk();
        $p->refresh();
        $this->assertTrue((bool) $p->is_muted_by_host);

        // 4. المعلم يسمح للطالب بالتحدث ويخفض يده
        $allowRes = $this->actingAs($admin)->postJson(route('classroom.controls', $roomId), [
            'action' => 'allow_speak',
            'user_id' => $student->id,
        ]);
        $allowRes->assertOk();
        $p->refresh();
        $this->assertFalse((bool) $p->is_muted_by_host);
        $this->assertFalse((bool) $p->is_hand_raised);

        // 5. محاولة طالب غير مصرح له بتغيير أدوات التحكم يُرفض بكود 403
        $unauthRes = $this->actingAs($student)->postJson(route('classroom.controls', $roomId), [
            'action' => 'toggle_policy',
        ]);
        $unauthRes->assertStatus(403);

        // تنظيف
        ClassroomParticipant::where('room_id', $roomId)->delete();
        ClassroomMessage::where('room_id', $roomId)->delete();
        ClassroomRoomControl::where('room_id', $roomId)->delete();
        $student->delete();
    }

    public function test_teacher_can_update_whiteboard_and_laser_pointer_and_student_syncs(): void
    {
        $admin = $this->getAdmin();
        $roomId = 'test-room-wb-'.rand(100, 999);

        $student = User::create([
            'name' => 'طالب السبورة',
            'email' => null,
            'phone_whatsapp' => '96773'.rand(1000000, 9999999),
            'password' => Hash::make('Secret123!'),
            'role' => 'student',
            'status' => 'active',
            'is_active' => true,
        ]);

        // 1. المعلم يفتح كتاب في مساحة الشرح
        $setBgRes = $this->actingAs($admin)->postJson(route('classroom.whiteboard', $roomId), [
            'action' => 'set_background',
            'bg_type' => 'pdf',
            'bg_url' => 'https://example.com/books/grammar.pdf',
            'bg_title' => 'كتاب القواعد الأساسية',
            'page_num' => 3,
        ]);
        $setBgRes->assertOk();
        $setBgRes->assertJson(['success' => true]);

        // 2. المعلم يحفظ خطوط رسم على السبورة
        $saveStateRes = $this->actingAs($admin)->postJson(route('classroom.whiteboard', $roomId), [
            'action' => 'save_state',
            'state' => [
                'bg_type' => 'pdf',
                'bg_url' => 'https://example.com/books/grammar.pdf',
                'bg_title' => 'كتاب القواعد الأساسية',
                'page_num' => 3,
                'strokes' => [
                    [
                        'tool' => 'pen',
                        'color' => '#ef4444',
                        'size' => 4,
                        'points' => [['x' => 10, 'y' => 20], ['x' => 30, 'y' => 40]],
                    ],
                    [
                        'tool' => 'highlighter',
                        'color' => '#facc15',
                        'size' => 18,
                        'points' => [['x' => 100, 'y' => 120], ['x' => 200, 'y' => 120]],
                    ],
                ],
            ],
        ]);
        $saveStateRes->assertOk();
        $this->assertEquals(2, $saveStateRes->json('whiteboard_version'));

        // 3. المعلم يحرك مؤشر الليزر
        $laserRes = $this->actingAs($admin)->postJson(route('classroom.whiteboard', $roomId), [
            'action' => 'laser_move',
            'x' => 0.45,
            'y' => 0.60,
            'active' => true,
        ]);
        $laserRes->assertOk();

        // 4. الطالب يقوم بالمزامنة فيحصل على السبورة ومؤشر الليزر
        $syncRes = $this->actingAs($student)->getJson(route('classroom.sync', ['roomId' => $roomId, 'whiteboard_version' => 0]));
        $syncRes->assertOk();
        $syncRes->assertJson([
            'success' => true,
            'whiteboard_version' => 2,
        ]);
        $this->assertEquals('كتاب القواعد الأساسية', $syncRes->json('whiteboard_state.bg_title'));
        $this->assertEquals(0.45, $syncRes->json('laser_state.x'));
        $this->assertEquals(0.60, $syncRes->json('laser_state.y'));
        $this->assertTrue($syncRes->json('laser_state.active'));

        // 5. محاولة طالب الرسم دون إذن تعيد 403
        $studentWbRes = $this->actingAs($student)->postJson(route('classroom.whiteboard', $roomId), [
            'action' => 'clear',
        ]);
        $studentWbRes->assertStatus(403);

        // 6. المعلم يمسح السبورة
        $clearRes = $this->actingAs($admin)->postJson(route('classroom.whiteboard', $roomId), [
            'action' => 'clear',
        ]);
        $clearRes->assertOk();
        $this->assertEquals(3, $clearRes->json('whiteboard_version'));

        // تنظيف
        ClassroomParticipant::where('room_id', $roomId)->delete();
        ClassroomMessage::where('room_id', $roomId)->delete();
        ClassroomRoomControl::where('room_id', $roomId)->delete();
        $student->delete();
    }

    public function test_whiteboard_browser_and_video_synchronization(): void
    {
        $admin = $this->getAdmin();
        $student = $this->getStudent();
        $roomId = 'test-room-wb-media-'.rand(100, 999);

        // إنشاء سجل التحكم بالفصل
        ClassroomRoomControl::create([
            'room_id' => $roomId,
            'is_live' => true,
            'requires_admission' => false,
            'whiteboard_version' => 0,
        ]);

        // انضمام الطالب
        ClassroomParticipant::create([
            'room_id' => $roomId,
            'user_id' => $student->id,
            'peer_id' => 'peer_student_media',
            'admission_status' => 'admitted',
            'last_seen_at' => now(),
        ]);

        // 1. المعلم يفتح متصفح جوجل داخل السبورة
        $browserRes = $this->actingAs($admin)->postJson(route('classroom.whiteboard', $roomId), [
            'action' => 'set_background',
            'bg_type' => 'browser',
            'bg_url' => 'https://www.google.com/search?igu=1&q=kids+educational+games',
            'bg_title' => 'بحث جوجل: kids educational games',
        ]);
        $browserRes->assertOk();
        $this->assertEquals(1, $browserRes->json('whiteboard_version'));

        // 2. مزامنة الطالب وتحقق من استلام المتصفح
        $syncStudent1 = $this->actingAs($student)->getJson(route('classroom.sync', ['roomId' => $roomId, 'whiteboard_version' => 0]));
        $syncStudent1->assertOk();
        $this->assertEquals('browser', $syncStudent1->json('whiteboard_state.bg_type'));
        $this->assertStringContainsString('google.com/search', $syncStudent1->json('whiteboard_state.bg_url'));

        // 3. المعلم يرفع ملف فيديو محلي MP4 للشرح عليه
        $fakeVideo = UploadedFile::fake()->create('science_lesson.mp4', 500, 'video/mp4');
        $uploadVideoRes = $this->actingAs($admin)->postJson(route('classroom.whiteboard', $roomId), [
            'action' => 'upload_bg_file',
            'file' => $fakeVideo,
        ]);
        $uploadVideoRes->assertOk();
        $this->assertEquals('video', $uploadVideoRes->json('bg_type'));
        $this->assertEquals(2, $uploadVideoRes->json('whiteboard_version'));

        // 4. مزامنة الطالب وتحقق من استلام الفيديو
        $syncStudent2 = $this->actingAs($student)->getJson(route('classroom.sync', ['roomId' => $roomId, 'whiteboard_version' => 1]));
        $syncStudent2->assertOk();
        $this->assertEquals(2, $syncStudent2->json('whiteboard_version'));
        $this->assertEquals('video', $syncStudent2->json('whiteboard_state.bg_type'));
        $this->assertStringContainsString('science-lesson', $syncStudent2->json('whiteboard_state.bg_url'));

        // تنظيف
        ClassroomParticipant::where('room_id', $roomId)->delete();
        ClassroomMessage::where('room_id', $roomId)->delete();
        ClassroomRoomControl::where('room_id', $roomId)->delete();
        $student->delete();
    }
}
