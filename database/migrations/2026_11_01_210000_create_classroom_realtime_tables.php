<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. جدول الحاضرين والمتصلين بالبث الحي في الوقت الحقيقي
        Schema::create('classroom_participants', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('peer_id')->index();
            $table->string('role')->default('student');
            $table->boolean('has_camera')->default(false);
            $table->boolean('has_mic')->default(true);
            $table->boolean('is_hand_raised')->default(false);
            $table->boolean('is_muted_by_host')->default(false);
            $table->timestamp('last_seen_at')->useCurrent();
            $table->timestamps();

            $table->unique(['room_id', 'user_id']);
        });

        // 2. جدول إشارات الربط الصوتي والفيديو عبر WebRTC (Signaling: Offers, Answers, ICE Candidates)
        Schema::create('classroom_signals', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->index();
            $table->string('from_peer_id')->index();
            $table->string('to_peer_id')->index();
            $table->string('signal_type', 32); // 'offer', 'answer', 'candidate'
            $table->longText('payload');
            $table->timestamp('created_at')->useCurrent()->index();
        });

        // 3. جدول رسائل وتفاعلات المحادثة الحية في الغرفة (نصوص، تسجيلات صوتية، أوراق إجابة)
        Schema::create('classroom_messages', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->default('text'); // text, audio, image, worksheet, system
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamps();
        });

        // 4. جدول إدارة وسياسات الغرفة الحية (كتم الجميع، الكلام بإذن، المطرودين)
        Schema::create('classroom_room_controls', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->unique();
            $table->string('policy')->default('open'); // 'open' (الجميع مسموح له) أو 'permission' (بإذن الأستاذ فقط)
            $table->boolean('is_live')->default(true);
            $table->json('kicked_user_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom_room_controls');
        Schema::dropIfExists('classroom_messages');
        Schema::dropIfExists('classroom_signals');
        Schema::dropIfExists('classroom_participants');
    }
};
