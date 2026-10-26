@php
    $navUser = auth()->user();
    $isTraineeNav = $navUser?->isCustomer();
    $navClass = fn (string ...$names) => request()->routeIs(...$names) ? 'nav-item active' : 'nav-item';
@endphp
@if ($isTraineeNav)
    <a href="{{ route('customer.dashboard') }}" class="{{ $navClass('customer.dashboard') }}"><svg class="ic"><use href="#i-dash"/></svg> My Dashboard</a>
    <p class="nav-label">My Fitness</p>
    <a href="{{ route('customer.trainer') }}" class="{{ $navClass('customer.trainer') }}"><svg class="ic"><use href="#i-user"/></svg> Your trainer</a>
    <a href="{{ route('customer.program') }}" class="{{ $navClass('customer.program') }}"><svg class="ic"><use href="#i-dumbbell"/></svg> My Program</a>
    <a href="{{ route('customer.schedule') }}" class="{{ $navClass('customer.schedule') }}"><svg class="ic"><use href="#i-calendar"/></svg> Schedule</a>
    <a href="{{ route('customer.progress') }}" class="{{ $navClass('customer.progress') }}"><svg class="ic"><use href="#i-trend"/></svg> Progress</a>
    <a href="{{ route('customer.nutrition') }}" class="{{ $navClass('customer.nutrition') }}"><svg class="ic"><use href="#i-leaf"/></svg> Nutrition</a>
    <a href="{{ route('customer.messages.index') }}" class="{{ $navClass('customer.messages.index') }}"><svg class="ic"><use href="#i-msg"/></svg> Message coach</a>
    <p class="nav-label">Account</p>
    <a href="{{ route('customer.profile.edit') }}" class="{{ $navClass('customer.profile.edit') }}"><svg class="ic"><use href="#i-user"/></svg> Profile</a>
    <a href="{{ route('customer.subscription') }}" class="{{ $navClass('customer.subscription') }}"><svg class="ic"><use href="#i-card"/></svg> Subscription</a>
    <a href="{{ route('customer.notifications') }}" class="{{ $navClass('customer.notifications') }}"><svg class="ic"><use href="#i-bell"/></svg> Notifications</a>
@else
    <a href="{{ route('admin.dashboard') }}" class="{{ $navClass('admin.dashboard') }}"><svg class="ic"><use href="#i-dash"/></svg> Dashboard</a>
    <p class="nav-label">Inbox</p>
    <a href="{{ route('admin.messages.index') }}" class="{{ $navClass('admin.messages.index') }}"><svg class="ic"><use href="#i-msg"/></svg> Trainee messages</a>
    <a href="{{ route('admin.nutrition.index') }}" class="{{ $navClass('admin.nutrition.index') }}"><svg class="ic"><use href="#i-clipboard"/></svg> Nutrition plans</a>
    <a href="{{ route('admin.trainees') }}" class="{{ $navClass('admin.trainees') }}"><svg class="ic"><use href="#i-run"/></svg> Trainees</a>
    <p class="nav-label">Account</p>
    <a href="{{ route('admin.profile.edit') }}" class="{{ $navClass('admin.profile.edit') }}"><svg class="ic"><use href="#i-user"/></svg> Profile settings</a>
@endif
