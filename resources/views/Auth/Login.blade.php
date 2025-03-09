<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ asset('/') }}assets/"
    data-template="vertical-menu-template-free"
>
    <head>

        @include('Components.Meta')

        @include('Components.Style')
        <link rel="stylesheet" href="{{ asset('/') }}assets/vendor/css/pages/page-auth.css" />

    </head>

    <body>

        <div class="container-xxl">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner">
                    <div class="card">
                        <div class="card-body">
                            <!-- Logo -->
                            <div class="justify-content-center align-items-center d-flex">
                                <a href="index.html" class="app-brand-link gap-2">
                                    <span class="app-brand-text demo text-body fw-bolder text-uppercase">SERBA CEBAN</span>
                                </a>
                            </div>
                            <p class="mb-4 text-center">Login ke dalam akun anda untuk melanjutkan</p>

                            <form id="formAuthentication" class="mb-3" action="{{ route('login.process') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="Masukkan alamat email anda"
                                        autofocus
                                    />
                                    @error('email')<small class="text-danger"><em>{{ $message }}</em></small>@enderror
                                </div>
                                <div class="mb-3 form-password-toggle">
                                    <label class="form-label" for="password">Password</label>
                                    <div class="input-group input-group-merge">
                                        <input
                                            type="password"
                                            id="password"
                                            class="form-control"
                                            name="password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="password"
                                        />
                                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                    </div>
                                    @error('password')<small class="text-danger"><em>{{ $message }}</em></small>@enderror
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember-me" />
                                        <label class="form-check-label" for="remember-me"> Remember Me </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                                </div>
                            </form>

                            <p class="text-center">
                                <span>Belum punya akun?</span>
                                <a href="{{ route('register') }}">
                                    <span>Buat akun</span>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('Components.Script')

        @if (Session::has('errorData'))
            <script>
                window.onload = function() {
                    Swal.fire({
                        text: "{{ Session::get('errorData') }}",
                        icon: "error"
                    });
                };
            </script>
        @endif

        @if (Session::has('success'))
            <script>
                window.onload = function() {
                    Swal.fire({
                        text: "{{ Session::get('success') }}",
                        icon: "success"
                    });
                };
            </script>
        @endif


    </body>

</html>
