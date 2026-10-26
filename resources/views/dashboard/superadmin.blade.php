@extends('layouts.superadmin')

@section('title', 'Dashboard')
@section('heading', 'Super Admin Dashboard')

@section('content')
@php
    $me = auth()->user();
    $initialsOf = fn ($name) => collect(preg_split('/\s+/', trim($name ?: 'TFI')))
        ->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');
    $greet = \App\Support\DashboardGreeting::line($me->name);
    $total = max(1, (int) $stats['total_users']);
    $pct = fn ($n) => round($n / $total * 100, 1);
    $websiteUrl = url('/');
@endphp

            <section class="hero">
                <div>
                    <div class="eyebrow">{{ $greet }}</div>
                    <h2>Welcome back, <span class="hl">{{ $me->name }}</span>!</h2>
                    <p>You have <strong>full system access</strong>. Edit every public page and plan price from this dashboard.</p>
                    <div class="hero-cta">
                        <a href="{{ route('superadmin.home.edit') }}" class="btn-gold"><svg class="ic"><use href="#i-file"/></svg> Edit home page</a>
                        <a href="{{ route('superadmin.prices.edit') }}" class="btn-ghost"><svg class="ic"><use href="#i-sliders"/></svg> Plan prices</a>
                    </div>
                </div>

                <div class="ring-card">
                    <div class="ring" style="--off: {{ $ringOff }};">
                        <svg viewBox="0 0 120 120"><circle class="track" cx="60" cy="60" r="52"/><circle class="bar" cx="60" cy="60" r="52"/></svg>
                        <div class="ring-val"><b>{{ $activePct }}<i>%</i></b></div>
                    </div>
                    <div class="ring-cap">Users active<br>{{ $stats['active_users'] }} of {{ $stats['total_users'] }}</div>
                </div>
            </section>

            <section class="stats">
                <a class="stat" href="{{ route('superadmin.users.index') }}">
                    <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-users"/></svg></span><span class="chip"><svg class="ic"><use href="#i-up"/></svg>All roles</span></div>
                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Total users</div>
                </a>
                <a class="stat" href="{{ route('superadmin.users.index', ['role' => 'admin']) }}">
                    <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-shield-check"/></svg></span></div>
                    <div class="stat-value">{{ $stats['total_admins'] }}</div>
                    <div class="stat-label">Admins</div>
                </a>
                <a class="stat" href="{{ route('superadmin.users.index', ['role' => 'customer']) }}">
                    <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-run"/></svg></span></div>
                    <div class="stat-value">{{ $stats['total_customers'] }}</div>
                    <div class="stat-label">Customers</div>
                </a>
                <a class="stat" href="{{ route('superadmin.enquiries.index') }}">
                    <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-bell"/></svg></span>@if ($stats['new_enquiries'] > 0)<span class="chip">{{ $stats['new_enquiries'] }} new</span>@endif</div>
                    <div class="stat-value">{{ $stats['total_enquiries'] }}</div>
                    <div class="stat-label">Enquiries</div>
                </a>
            </section>

            <section class="grid-2">
                <div class="card">
                    <div class="card-head">
                        <h3><svg class="ic"><use href="#i-pie"/></svg> User mix</h3>
                    </div>
                    <div class="dist-bar" role="img" aria-label="User distribution by role">
                        <span class="s1" style="flex: {{ $admins }} 1 0"></span>
                        <span class="s2" style="flex: {{ $customers }} 1 0"></span>
                        @if ($others > 0)<span class="s3" style="flex: {{ $others }} 1 0"></span>@endif
                    </div>
                    <div class="legend">
                        <div class="legend-row"><i class="s1"></i> Admins <b>{{ $admins }}</b><small>{{ $pct($admins) }}%</small></div>
                        <div class="legend-row"><i class="s2"></i> Customers <b>{{ $customers }}</b><small>{{ $pct($customers) }}%</small></div>
                        @if ($others > 0)
                            <div class="legend-row"><i class="s3"></i> Super admins &amp; others <b>{{ $others }}</b><small>{{ $pct($others) }}%</small></div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h3><svg class="ic"><use href="#i-zap"/></svg> Quick actions</h3>
                    </div>
                    <div class="actions">
                        <a href="{{ route('superadmin.users.index') }}" class="qa"><span class="tile"><svg class="ic"><use href="#i-users"/></svg></span><b>All users</b></a>
                        <a href="{{ route('superadmin.users.create', ['role' => 'admin']) }}" class="qa"><span class="tile"><svg class="ic"><use href="#i-user-plus"/></svg></span><b>Add admin</b></a>
                        <a href="{{ route('superadmin.enquiries.index') }}" class="qa"><span class="tile"><svg class="ic"><use href="#i-bell"/></svg></span><b>Enquiries</b></a>
                    </div>
                </div>
            </section>

            <section class="card flush">
                <div class="card-head">
                    <h3><svg class="ic"><use href="#i-clock"/></svg> Recent users</h3>
                    <a href="{{ route('superadmin.users.index') }}" class="link">View all <svg class="ic"><use href="#i-arrow"/></svg></a>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentUsers as $user)
                            <tr>
                                <td>
                                    <div class="td-name">
                                        <div class="td-avatar">
                                            @if ($user->avatarUrl())
                                                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}">
                                            @else
                                                {{ $initialsOf($user->name) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="td-main">{{ $user->name }}</div>
                                            <div class="td-sub">{{ $user->phone ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="muted">{{ $user->email }}</td>
                                <td><span class="badge role-{{ $user->role }}">{{ \Illuminate\Support\Str::headline($user->role) }}</span></td>
                                <td>
                                    <span class="badge {{ $user->is_active ? 'st-active' : 'st-inactive' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="muted" style="font-size:.82rem; white-space:nowrap">{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="empty">No users yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="card flush">
                <div class="card-head">
                    <h3><svg class="ic"><use href="#i-bell"/></svg> Latest enquiries</h3>
                    <a href="{{ route('superadmin.enquiries.index') }}" class="link">View all <svg class="ic"><use href="#i-arrow"/></svg></a>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Person</th>
                                <th>Program</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentEnquiries as $enquiry)
                            <tr>
                                <td>
                                    <div class="td-main">{{ $enquiry->name }}</div>
                                    <div class="td-sub">{{ $enquiry->email }} · {{ $enquiry->phone }}</div>
                                </td>
                                <td>{{ $enquiry->program }}</td>
                                <td class="muted">{{ \Illuminate\Support\Str::limit($enquiry->message ?: '—', 60) }}</td>
                                <td>
                                    <span class="badge {{ $enquiry->isUnread() ? 'st-active' : 'st-inactive' }}">
                                        {{ $enquiry->isUnread() ? 'New' : 'Read' }}
                                    </span>
                                </td>
                                <td>
                                    <a class="link" href="{{ route('superadmin.enquiries.show', $enquiry) }}">{{ $enquiry->created_at->format('d M Y') }} <svg class="ic"><use href="#i-arrow"/></svg></a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="empty">No website enquiries yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="site-cta">
                <div>
                    <h3>See what your members see on the <span>TFI website</span></h3>
                    <p>Check programs, pricing and trainer pages exactly as customers do.</p>
                </div>
                <a href="{{ $websiteUrl }}" class="btn-gold full-sm">
                    <svg class="ic"><use href="#i-globe"/></svg> Go to website <svg class="ic"><use href="#i-up-right"/></svg>
                </a>
            </section>
@endsection
