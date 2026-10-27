<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BannerManagementTest extends TestCase
{
    protected function getAdminUser(): User
    {
        return User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_banners_index(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.banners.index'));

        $response->assertStatus(200);
        $response->assertSee('إدارة وتعديل الإعلانات والحملات');
    }

    public function test_admin_can_create_banner_with_uploaded_image(): void
    {
        $admin = $this->getAdminUser();

        $image = UploadedFile::fake()->image('announcement_banner.jpg', 800, 400);

        $response = $this->actingAs($admin)->post(route('admin.banners.store'), [
            'title' => 'إعلان ترويجي اختباري مع صورة',
            'title_en' => 'Test Promo Banner With Image',
            'subtitle' => 'عرض خاص ومحدد',
            'content' => 'تفاصيل الإعلان الترويجي بالكامل',
            'placement' => 'hero_slider',
            'theme_style' => 'amber',
            'link_url' => '/onboarding',
            'button_text' => 'سجل الآن',
            'image' => $image,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $banner = Banner::where('title', 'إعلان ترويجي اختباري مع صورة')->first();
        $this->assertNotNull($banner);
        $this->assertEquals('all', $banner->device_target);
        $this->assertNotNull($banner->image_path);
        $this->assertFileExists(public_path($banner->image_path));

        // تنظيف الملف التجريبي بعد الفحص
        if (file_exists(public_path($banner->image_path))) {
            @unlink(public_path($banner->image_path));
        }
        $banner->delete();
    }

    public function test_arabic_for_non_native_level_exists_in_courses_management(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.courses.index'));

        $response->assertStatus(200);
        $response->assertSee('دورات تكوينية في مجال اللغة العربية للناطقين بغيرها');

        $level = Level::where('name_ar', 'دورات تكوينية في مجال اللغة العربية للناطقين بغيرها')->first();
        $this->assertNotNull($level);
        $this->assertGreaterThanOrEqual(1, $level->courses()->count());
    }

    public function test_home_page_displays_arabic_for_non_native_pillar(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('دورات تكوينية في مجال اللغة العربية للناطقين بغيرها');
    }

    public function test_pricing_and_services_page_displays_arabic_level(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('accounting.pricing.index'));

        $response->assertStatus(200);
        $response->assertSee('دورات تكوينية في مجال اللغة العربية للناطقين بغيرها');
    }

    public function test_curriculum_page_displays_arabic_level(): void
    {
        $response = $this->get(route('curriculum'));

        $response->assertStatus(200);
        $response->assertSee('دورات تكوينية في مجال اللغة العربية للناطقين بغيرها');
    }
}
