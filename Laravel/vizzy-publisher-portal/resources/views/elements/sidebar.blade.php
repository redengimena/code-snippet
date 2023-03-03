<!--**********************************
            Sidebar start
***********************************-->

<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <li>
                <a href="{!! url('/'); !!}" class="ai-icon studio-sidebar-item" aria-expanded="false">
                    <i class="flaticon-381-controls-3"></i>
                    <span class="nav-text">Podcast Studio</span>
                </a>
            </li>
            <li>
                <a class="has-arrow ai-icon podcast-sidebar-item" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-television"></i>
                    <span class="nav-text">My Podcasts</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{!! route('podcasts') !!}">My Shows</a></li>
                    <!-- <li><a href="{!! route('add-podcast') !!}">Add Show</a></li>-->
                    <li><a href="{!! route('vizzies') !!}">My Vizzy's</a></li>
                </ul>
            </li>
            @role('admin')
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-settings-2"></i>
                    <span class="nav-text">Administration</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{!! route('admin.dashboard') !!}">Dashboard</a></li>
                    <li><a href="{!! route('admin.users.index') !!}">Podcasters</a></li>
                    <li><a href="{!! route('admin.podcast-categories.index') !!}">Podcast Categories</a></li>
                    <li><a href="{!! route('admin.podcasts.index') !!}">Podcasts Claimed by Admin</a></li>
                    <li><a href="{!! route('admin.vizzys.index') !!}">Vizzys</a></li>
                    <li><a href="{!! route('admin.top-shows.index') !!}">Top Shows</a></li>
                    <li><a href="{!! route('admin.export.index') !!}">Export Usage</a></li>
                </ul>
            </li>
            @endrole
            <li class="nav-item dropdown header-profile">
                <a class="nav-link" href="javascript:void()" data-toggle="dropdown"  aria-expanded="false" >
                    <div class="profile-image">
                        @if (Auth::user()->image)
                        <img  src="{{ Auth::user()->image }}" width="20" alt=""/>
                        @else
                        <img  src="{{ asset('images/profile/avatar.png') }}" width="20" alt=""/>
                        @endif
                    </div>
                    <div class="header-info">
                        @impersonating()
                        <p class="fs-12 mb-0">Impersonating</p>
                        @endImpersonating
                        <span>{{ Auth::user()->firstname }}</span>
                        @if (Auth::user()->hasRole('admin'))
                        <p class="fs-12 mb-0">Super Admin</p>
                        @else
                        <p class="fs-12 mb-0">Account Owner</p>
                        @endif
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{!! url('/profile'); !!}" class="dropdown-item ai-icon">
                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span class="ml-2">Profile </span>
                    </a>
                    <a href="{!! url('/help'); !!}" class="dropdown-item ai-icon">
                        <svg id="icon-help" xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <span class="ml-2">Help </span>
                    </a>
                    @impersonating()
                    <a href="{{ route('impersonate.leave') }}" class="dropdown-item ai-icon">
                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span class="ml-2">Logout As</span>
                    </a>
                    @endImpersonating
                    @notImpersonating()
                    <a href="{{ route('logout') }}" class="dropdown-item ai-icon" onclick="event.preventDefault();
                                      document.getElementById('logout-form').submit();">
                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span class="ml-2">Logout </span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    @endNotImpersonating
                </div>
            </li>
        </ul>

    </div>
</div>