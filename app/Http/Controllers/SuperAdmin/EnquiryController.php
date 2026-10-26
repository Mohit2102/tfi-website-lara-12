<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));

        $enquiries = Enquiry::query()
            ->when($status === 'new', fn ($query) => $query->unread())
            ->when($status === 'read', fn ($query) => $query->whereNotNull('read_at'))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('program', 'like', '%'.$search.'%')
                        ->orWhere('message', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.enquiries.index', [
            'enquiries' => $enquiries,
            'status' => in_array($status, ['new', 'read'], true) ? $status : null,
            'search' => $search,
            'counts' => [
                'all' => Enquiry::count(),
                'new' => Enquiry::query()->unread()->count(),
                'read' => Enquiry::query()->whereNotNull('read_at')->count(),
            ],
        ]);
    }

    public function show(Enquiry $enquiry): View
    {
        $enquiry->markRead();

        return view('superadmin.enquiries.show', [
            'enquiry' => $enquiry->fresh(),
        ]);
    }

    public function destroy(Enquiry $enquiry): RedirectResponse
    {
        $enquiry->delete();

        return redirect()
            ->route('superadmin.enquiries.index')
            ->with('success', 'Enquiry removed.');
    }
}
