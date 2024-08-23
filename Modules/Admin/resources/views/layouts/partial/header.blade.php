   <div class="dashboard-topbar">
    <div class="topbar-left">
        <div class="toggler-icon">
            <img src="{{ asset('images/toggle-button.svg') }}" class="img-fluid">
        </div>
       <!--  <div class="search-form">
            <form>
                <input type="search" class="form-search" placeholder="Search anything...">
            </form>
        </div> -->
    </div>
    <div class="topbar-right">
        <ul>
            @can('escrow_deposit')
            <li>
                <button type="button" data-toggle="modal" data-target="#EscrowDepositModal" class="btn btn-success">Deposit </button>
            </li>
            @endcan

            <li>
                @can('escrow_access')
                @php $feeAcc = getFeeAccInfo(-2);
                $siteStatus = onboardStatus(); @endphp
                @if(isset($feeAcc) && $feeAcc->site_id == epcache('site_id'))
                <div class="balance-item">MAIN A/C BALANCE :<span><img src="{{ asset('images/currency.svg') }}" class="img-fluid">{{getEscrowBalance(1)}}</span></div>
                <div class="balance-item">FEE A/C BALANCE :<span><img src="{{ asset('images/currency.svg') }}" class="img-fluid">{{getescrowbalancefee(1)}}</span></div>
                @else
                    @if($siteStatus > 5)
                    <div class="balance-item">ESCROW BALANCE :<span><img src="{{ asset('images/currency.svg') }}" class="img-fluid">{{getEscrowBalance(1)}}</span></div>
                    @endif
                @endif
                @endcan
               <!--  <div class="notification-icon custom-dropdown">
                    <a href="javascript:void(0)">
                        <img src="{{ asset('images/notification-icon.svg') }}" class="img-fluid">
                        <span class="notification-dot"></span>
                    </a>
                    <div class="notification-dropdown">
                        <ul>
                            <li>
                                <a href="#"><img src="{{ asset('images/noti-bar.svg') }}" class="img-fluid">No More Notifications!</a>
                            </li>
                        </ul>
                    </div>
                </div> -->
            </li>
            <li class="profile_icon">
                <div class="admin-info custom-dropdown">
                    <a href="javascript:void(0)">{{ isset(Auth::user()->name) ? getFirstChar(Auth::user()->name) : '' }}</a>
                    <div class="organization">
                        <h6>{{ isset(Auth::user()->name) ? Auth::user()->name : '' }}</h6>
                    </div>
                    <div class="notification-dropdown profile-dropdown">
                        <ul>
                            <li>
                                <em class="icon ni ni-user-alt"></em>
                                <a href="{{route('profile.view')}}">Profile</a>
                            </li>
                            @role('Superadmin')
                            <li><em class="icon ni ni-reload-alt"></em><a href="{{route('clear.cache')}}">Cache Clear</a></li>
                            <li><em class="icon ni ni-regen-alt"></em><a href="{{route('clear.config')}}">Config Clear</a></li>
                            @endrole
                            <li>
                                <em class="icon ni ni-signout"></em>
                                <a href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();" >Logout</a>
                                <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
