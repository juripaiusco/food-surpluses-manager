<ul class="navbar-nav mr-auto">

    @php
        $modules = json_decode(Auth::user()->json_modules, true);
    @endphp

    @if(isset($modules['shop']) && $modules['shop'] == 'on')
    <li class="nav-item {{ Route::is('shop*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('shop') }}">{{ __('layout.shop.title') }}</a>
    </li>
    @endif

    @if(isset($modules['orders']) && $modules['orders'] == 'on')
    <li class="nav-item {{ Route::is('orders*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('orders') }}">{{ __('layout.orders.title') }}</a>
    </li>
    @endif

    @if(isset($modules['products']) && $modules['products'] == 'on')
    <li class="nav-item {{ Route::is('products*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('products') }}">{{ __('layout.products.title') }}</a>
    </li>
    @endif

    @if(isset($modules['store']) && $modules['store'] == 'on')
    <li class="nav-item {{ Route::is('store*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('store') }}">{{ __('layout.store.title') }}</a>
    </li>
    @endif

    @if(isset($modules['customers']) && $modules['customers'] == 'on')
    <li class="nav-item {{ Route::is('customers*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('customers') }}">{{ __('layout.customers.title') }}</a>
    </li>
    @endif

    @if(isset($modules['users']) && $modules['users'] == 'on')
    <li class="nav-item {{ Route::is('users*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users') }}">{{ __('layout.users.title') }}</a>
    </li>
    @endif

</ul>
