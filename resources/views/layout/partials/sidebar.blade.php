<nav class="sidebar">
  <div class="sidebar-header">
    <a href="#" class="sidebar-brand">
      {{__('Dashboard')}}
    </a>
    <div class="sidebar-toggler not-active">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
  <div class="sidebar-body">
    <ul class="nav" id="sidebarNav">
        <li class="nav-item {{ active_class(['/']) }}">
        <a href="{{ url('/') }}" class="nav-link">
          <i class="link-icon" data-lucide="home"></i>
          <span class="link-title">Dashboard</span>
        </a>
      </li>
        <li class="nav-item {{ active_class(['user-*']) }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#user" role="button"
               aria-expanded="{{ is_active_route(['user-*']) }}" aria-controls="user">
                <i class="link-icon" data-lucide="users"></i>
                <span class="link-title">User</span>
                <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse {{ show_class(['user-*']) }}" data-bs-parent="#sidebarNav" id="user">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('user.user-list') }}" class="nav-link {{ active_class(['user-list']) }}">User
                            List</a>
                    </li>

                </ul>
            </div>
        </li>
        <li class="nav-item {{ active_class(['product-*']) }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#product" role="button"
               aria-expanded="{{ is_active_route(['product-*']) }}" aria-controls="product">
                <i class="link-icon" data-lucide="archive"></i>
                <span class="link-title">Product</span>
                <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse {{ show_class(['product-*']) }}" data-bs-parent="#sidebarNav" id="product">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('product.product-list') }}"
                           class="nav-link {{ active_class(['product-list']) }}">Product List</a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('product.create-product') }}"
                           class="nav-link {{ active_class(['product-create']) }}">Create Product</a>
                    </li>

                </ul>
            </div>
        </li>
        <li class="nav-item {{ active_class(['sale-*']) }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#sale" role="button"
               aria-expanded="{{ is_active_route(['sale-*']) }}" aria-controls="sale">
                <i class="link-icon" data-lucide="dollar-sign"></i>
                <span class="link-title">Sales</span>
                <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse {{ show_class(['sale-*']) }}" data-bs-parent="#sidebarNav" id="sale">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('sale.sale-list') }}" class="nav-link {{ active_class(['sale-list']) }}">Sale List</a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('sale.create-sale') }}" class="nav-link {{ active_class(['sale-create']) }}">Create Sale</a>
                    </li>

                </ul>
            </div>
        </li>
    </ul>
  </div>
</nav>
