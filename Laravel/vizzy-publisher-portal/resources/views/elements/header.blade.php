        <!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">
                                @yield('title', $page_title ?? 'Dashboard')
                            </div>
                        </div>
                        <div class="header-right">
                            @yield('header-right')
                        </div>
                    </div>
                </nav>
            </div>
        </div>