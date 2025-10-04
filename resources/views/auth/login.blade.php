@extends('layout.master2')

@section('content')
<div class="row w-100 mx-0 auth-page">
  <div class="col-md-8 col-xl-6 mx-auto">
    <div class="card">
      <div class="row">
        <div class="col-md-12 ps-md-0">
          <div class="auth-form-wrapper px-4 py-5">
            <a href="#" class="nobleui-logo d-block mb-2 text-center">{{env('APP_NAME')}}</a>
            <h5 class="text-secondary fw-normal mb-4 text-center">Welcome back! Log in to your account.</h5>
              <form class="forms-sample" method="POST" action="{{ route('login.store') }}">
                  @csrf

                  <div class="mb-3">
                      <label for="userEmail" class="form-label">Email address</label>
                      <input type="email" class="form-control @error('email') is-invalid @enderror"
                             name="email" id="userEmail" placeholder="Email" value="{{ old('email') }}">
                      @error('email')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                  </div>

                  <div class="mb-3">
                      <label for="userPassword" class="form-label">Password</label>
                      <div class="input-group">
                          <input type="password"
                                 class="form-control @error('password') is-invalid @enderror"
                                 name="password" id="userPassword" placeholder="Password" autocomplete="current-password">
                          <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                          </span>
                      </div>
                      @error('password')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                  </div>

                  <div>
                      <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">Login</button>
                  </div>
              </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('custom-scripts')
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('userPassword');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
@endpush
