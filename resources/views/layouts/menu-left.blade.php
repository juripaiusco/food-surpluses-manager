<ul class="navbar-nav mr-auto">

    <li class="nav-item {{ Route::is('shop*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('shop') }}">{{ __('layout.shop.title') }}</a>
    </li>

    <li class="nav-item {{ Route::is('order*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('order') }}">{{ __('layout.order.title') }}</a>
    </li>

    <li class="nav-item {{ Route::is('products*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('products') }}">{{ __('layout.products.title') }}</a>
    </li>

    <li class="nav-item {{ Route::is('store*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('store') }}">{{ __('layout.store.title') }}</a>
    </li>

    <li class="nav-item {{ Route::is('customers*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('customers') }}">{{ __('layout.customers.title') }}</a>
    </li>

</ul>
