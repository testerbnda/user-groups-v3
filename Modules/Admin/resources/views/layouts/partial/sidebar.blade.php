<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="{{ url('admin/dashboard') }}" class="app-brand-link">
              <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/logo/opti-logo.png')}}" class="w-100"/>
              </span> 
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
           
      
         
            <li class="menu-item">
              <a
                href="{{ url('admin/dashboard') }}"
                class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Email">Dashboard</div>
              </a>
            </li>

            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div class="text-truncate" data-i18n="Account Settings">User Management</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="{{ route('user.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="Account">User List</div>
                  </a>
                </li>

                <li class="menu-item">
                  <a href="{{ route('sites.list') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="Account">Site List</div>
                  </a>
                </li>


                <li class="menu-item">
                  <a href="#" class="menu-link">
                    <div class="text-truncate" data-i18n="Account">Group List</div>
                  </a>
                </li>
                 
              </ul>
            </li>

            <li class="menu-item">
              <a
                href="javascript:void(0);"
                class="menu-link">
                <i class="menu-icon tf-icons bx bx-down-arrow-circle"></i>
                <div class="text-truncate" data-i18n="Email">Receivables</div>
              </a>
            </li>

            <li class="menu-item">
              <a
                href="javascript:void(0);"
                class="menu-link">
                <i class="menu-icon tf-icons bx bx-up-arrow-circle"></i>
                <div class="text-truncate" data-i18n="Email">Payables</div>
              </a>
            </li>

            <li class="menu-item">
              <a
                href="javascript:void(0);"
                class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div class="text-truncate" data-i18n="Email">Reporting</div>
              </a>
            </li>

          
            <!-- Pages -->
            <!-- <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div class="text-truncate" data-i18n="Account Settings">Account Settings</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="pages-account-settings-account.html" class="menu-link">
                    <div class="text-truncate" data-i18n="Account">Account</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="pages-account-settings-notifications.html" class="menu-link">
                    <div class="text-truncate" data-i18n="Notifications">Notifications</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="pages-account-settings-connections.html" class="menu-link">
                    <div class="text-truncate" data-i18n="Connections">Connections</div>
                  </a>
                </li>
              </ul>
            </li> -->
           
          
          </ul>
        </aside>