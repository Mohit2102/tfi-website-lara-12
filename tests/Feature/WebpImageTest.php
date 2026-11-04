<?php

namespace Tests\Feature;

use App\Support\WebpImage;
use Tests\TestCase;

class WebpImageTest extends TestCase
{
    private string $png;

    private string $webp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->png = public_path('images/__webp-test.png');
        $this->webp = public_path('images/__webp-test.webp');

        $image = imagecreatetruecolor(40, 20);
        imagepng($image, $this->png);
        imagedestroy($image);
    }

    protected function tearDown(): void
    {
        @unlink($this->png);
        @unlink($this->webp);

        parent::tearDown();
    }

    public function test_references_are_left_alone_without_a_webp_copy(): void
    {
        $html = '<img src="images/__webp-test.png">';

        $this->assertSame($html, WebpImage::rewrite($html));
    }

    public function test_references_point_at_a_generated_webp_copy(): void
    {
        $this->assertTrue(WebpImage::create($this->png));
        $this->assertFileExists($this->webp);

        $html = '<img src="/images/__webp-test.png"><div style="background-image:url(\'images/__webp-test.png\')"></div>';

        $this->assertSame(
            '<img src="/images/__webp-test.webp"><div style="background-image:url(\'images/__webp-test.webp\')"></div>',
            WebpImage::rewrite($html)
        );
    }
}
