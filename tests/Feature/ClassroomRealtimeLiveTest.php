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
}
