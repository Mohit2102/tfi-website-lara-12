<?php

namespace App\Support;

use Illuminate\Http\Request;

class VisitorCurrency
{
    /**
     * @var list<string>
     */
    private const COUNTRY_HEADERS = [
        'CF-IPCountry',
        'CloudFront-Viewer-Country',
        'X-Vercel-IP-Country',
        'X-Appengine-Country',
        'X-Country-Code',
    ];

    /**
     * @return array{country: ?string, currency: ?string}
     */
    public function hint(?Request $request = null): array
    {
        $request ??= request();
        $country = $this->countryFrom($request);

        if ($country === null) {
            return ['country' => null, 'currency' => null];
        }

        return [
            'country' => $country,
            'currency' => $country === 'IN' ? 'inr' : 'usd',
        ];
    }

    public function countryFrom(Request $request): ?string
    {
        foreach (self::COUNTRY_HEADERS as $header) {
            $code = strtoupper(trim((string) $request->header($header, '')));

            if ($code !== '' && $code !== 'XX' && $code !== 'T1' && preg_match('/^[A-Z]{2}$/', $code)) {
                return $code;
            }
        }

        return null;
    }
}
