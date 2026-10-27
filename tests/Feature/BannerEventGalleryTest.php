<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class BannerEventGalleryTest extends TestCase
{
    public function test_admin_can_create_event_gallery_item(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $response = $this->actingAs($admin)->post(route('admin.banners.store'), [
            'title' => 'حفل تخرج الدفعة 40 تجريبي',
            'title_en' => 'Graduation Batch 40 Test',
            'category' => 'event_gallery',
            'batch_name' => 'الدفعة 40 - دبلوم الإنجليزية',
            'event_date' => '2026-11-01',
            'event_location' => 'القاعة الملكية للاحتفالات',
            'subtitle' => 'حفل تخرج وتكريم الأوائل',
            'content' => 'تفاصيل الحفل والتكريم وتوزيع الشهادات الرسمية.',
            'placement' => 'hero_slider',
            'theme_style' => 'indigo',
            'link_url' => '#gallery',
            'button_text' => 'مشاهدة الألبوم 📸',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('banners', [
            'title' => 'حفل تخرج الدفعة 40 تجريبي',
            'category' => 'event_gallery',
            'batch_name' => 'الدفعة 40 - دبلوم الإنجليزية',
            'event_location' => 'القاعة الملكية للاحتفالات',
        ]);
    }

    public function test_homepage_separates_ads_and_events(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ])->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('معرض صور الفعاليات وحفلات التخرج والتكريم');
        $response->assertSee('graduationGallerySection');
    }

    public function test_announcements_page_displays_content(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ])->get(route('announcements.index'));

        $response->assertStatus(200);
        $response->assertSee('مركز الإعلانات ومعرض حفلات التخرج والفعاليات');
        $response->assertSee('حفلات التخرج والفعاليات');
    }
}
