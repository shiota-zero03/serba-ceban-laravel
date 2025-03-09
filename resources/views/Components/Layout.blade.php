<html>
    <head>
        @include('Components.Meta')

        @include('Components.Style')
    </head>
    <div>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">

                @include('Components.Sidebar')

                <div class="layout-page">
                    <div id="loading-set" class="d-none position-fixed align-items-center justify-content-center gap-2 text-primary" style="background: rgba(255,255,255,0.2); top: 0; left: 0; z-index: 9; width: 100%; height: 100vh;">
                        <div class="spinner-border text-primary" role="status"></div>
                        Loading ...
                    </div>

                    @include('Components.Navbar')

                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">

                            @yield('content')

                        </div>
                    </div>
                </div>
            </div>
        </div>


        @include('Components.Script')

        @stack('script')
    </div>
</html>
