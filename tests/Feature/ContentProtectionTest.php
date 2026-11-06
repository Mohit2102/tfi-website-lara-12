<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\ContentProtection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_served_protected_and_minified(): void
    {
        config(['app.content_protection' => true]);

        $response = $this->get('/')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertSee('<script src="/tfi-protect.js"></script>', false);

        $this->assertStringNotContainsString("\n    <", $response->getContent());
    }

    public function test_protection_can_be_disabled(): void
    {
        config(['app.content_protection' => false]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('tfi-protect.js', false);
    }

    public function test_super_admins_receive_unprotected_pages(): void
    {
        config(['app.content_protection' => true]);
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/')
            ->assertOk()
            ->assertDontSee('tfi-protect.js', false);
    }

    public function test_minify_keeps_whitespace_sensitive_blocks_intact(): void
    {
        $html = "<div>\n  <p>Hello   world</p>\n  <!-- note -->\n</div>"
            ."<script>\n// comment\nvar a = 1;\n</script>"
            ."<div class=\"eq\">TDEE = BMR\n\n  men    10w</div>"
            ."<style>\n  .a  {  color : red ; }\n</style>";

        $this->assertSame(
            "<div> <p>Hello world</p> </div>"
            ."<script>\n// comment\nvar a = 1;\n</script>"
            ."<div class=\"eq\">TDEE = BMR\n\n  men    10w</div>"
            .'<style>.a{color : red}</style>',
            ContentProtection::minify($html)
        );
    }
}
