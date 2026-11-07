<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Tests\TestCase;

class TypographySettingsTest extends TestCase
{
    protected function getAdminUser(): User
    {
        return User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_typography_settings(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.branding.typography.update'), [
            'site_font_family' => 'Readex Pro',
            'site_font_color' => '#1e293b',
            'site_font_color_muted' => '#64748b',
            'site_heading_color' => '#0066a1',
            'site_font_size' => '17px',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('Readex Pro', SiteSetting::get('site_font_family'));
        $this->assertEquals('#1e293b', SiteSetting::get('site_font_color'));
        $this->assertEquals('#64748b', SiteSetting::get('site_font_color_muted'));
        $this->assertEquals('#0066a1', SiteSetting::get('site_heading_color'));
        $this->assertEquals('17px', SiteSetting::get('site_font_size'));
    }

    public function test_admin_can_reset_typography_settings(): void
    {
        $admin = $this->getAdminUser();

        SiteSetting::set('site_font_family', 'Almarai', 'typography');
        SiteSetting::set('site_font_size', '18px', 'typography');

        $response = $this->actingAs($admin)->post(route('admin.branding.typography.reset'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertNull(SiteSetting::get('site_font_family'));
        $this->assertNull(SiteSetting::get('site_font_size'));
    }

    public function test_typography_validation_rejects_invalid_font_family(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.branding.typography.update'), [
            'site_font_family' => 'InvalidFontName',
            'site_font_size' => '16px',
        ]);

        $response->assertSessionHasErrors(['site_font_family']);
    }

    public function test_typography_validation_rejects_invalid_color(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.branding.typography.update'), [
            'site_font_family' => 'Cairo',
            'site_font_color' => 'not-a-color',
            'site_font_size' => '16px',
        ]);

        $response->assertSessionHasErrors(['site_font_color']);
    }

    public function test_guest_cannot_update_typography(): void
    {
        $response = $this->post(route('admin.branding.typography.update'), [
            'site_font_family' => 'Cairo',
            'site_font_size' => '16px',
        ]);

        $response->assertRedirect(route('login'));
    }
}
