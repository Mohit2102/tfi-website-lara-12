<?php

namespace Tests\Unit;

use App\Support\VisitorCurrency;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class VisitorCurrencyTest extends TestCase
{
    public function test_india_headers_select_inr(): void
    {
        $request = Request::create('/');
        $request->headers->set('CF-IPCountry', 'IN');

        $this->assertSame(
            ['country' => 'IN', 'currency' => 'inr'],
            (new VisitorCurrency)->hint($request)
        );
    }

    public function test_foreign_headers_select_usd(): void
    {
        $request = Request::create('/');
        $request->headers->set('CloudFront-Viewer-Country', 'US');

        $this->assertSame(
            ['country' => 'US', 'currency' => 'usd'],
            (new VisitorCurrency)->hint($request)
        );
    }

    public function test_missing_geo_leaves_currency_unset(): void
    {
        $this->assertSame(
            ['country' => null, 'currency' => null],
            (new VisitorCurrency)->hint(Request::create('/'))
        );
    }
}
