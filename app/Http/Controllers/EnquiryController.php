<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:255'],
            'program' => ['required', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        Enquiry::query()->create([
            ...$data,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255) ?: null,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Thanks — we received your enquiry and will reply within 24 hours.',
        ]);
    }
}
