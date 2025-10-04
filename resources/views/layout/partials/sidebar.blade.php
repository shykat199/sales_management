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
        <a class="nav-link" data-bs-toggle="collapse" href="#user" role="button" aria-expanded="{{ is_active_route(['user-*']) }}" aria-controls="user">
          <i class="link-icon" data-lucide="users"></i>
          <span class="link-title">User</span>
          <i class="link-arrow" data-lucide="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['user-*']) }}" data-bs-parent="#sidebarNav" id="user">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{ route('user.user-list') }}" class="nav-link {{ active_class(['user-list']) }}">User List</a>
            </li>

          </ul>
        </div>
      </li>
      <li class="nav-item {{ active_class(['auth/*']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#auth" role="button" aria-expanded="{{ is_active_route(['auth/*']) }}" aria-controls="auth">
          <i class="link-icon" data-lucide="unlock"></i>
          <span class="link-title">Authentication</span>
          <i class="link-arrow" data-lucide="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['auth/*']) }}" data-bs-parent="#sidebarNav" id="auth">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{ url('/auth/login') }}" class="nav-link {{ active_class(['auth/login']) }}">Login</a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/auth/register') }}" class="nav-link {{ active_class(['auth/register']) }}">Register</a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item {{ active_class(['error/*']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#error" role="button" aria-expanded="{{ is_active_route(['error/*']) }}" aria-controls="error">
          <i class="link-icon" data-lucide="cloud-off"></i>
          <span class="link-title">Error</span>
          <i class="link-arrow" data-lucide="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['error/*']) }}" data-bs-parent="#sidebarNav" id="error">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{ url('/error/404') }}" class="nav-link {{ active_class(['error/404']) }}">404</a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/error/500') }}" class="nav-link {{ active_class(['error/500']) }}">500</a>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</nav>
