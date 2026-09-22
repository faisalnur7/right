  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 customer_side_nav">
      <!-- Brand Logo -->
      <a href="{{ 'dashboard' }}" class="brand-link">
          <img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
              class="brand-image img-circle elevation-3" style="opacity: .8">
          <span class="brand-text font-weight-light">Right BD</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="mt-3 pb-3 mb-3 flex items-center justify-start gap-3 border-b border-b-gray-600">
              <div class="pl-1 flex w-16">
                  @php
                      $user_img = asset('assets/dist/img/user2-160x160.jpg');
                      if (auth()->user()->kyc && auth()->user()->kyc->photo) {
                          $user_img = asset('/kyc/photo/' . auth()->user()->kyc->photo);
                      }
                  @endphp
                  <img src="{{ $user_img }}" class="img-circle rounded-md flex w-full" alt="User Image">
              </div>
              <div class="flex flex-col gap-1">
                  <a href="#" class="font-extrabold">{{ auth()->user()->name }}</a>
                  <a href="#" class="font-bold">SL No.:
                      {{ auth()->user()->kyc ? auth()->user()->kyc->affiliate_id : '' }}</a>
              </div>
          </div>
          @php
              $affiliate_route = route('kyc.list');
              $affiliate_type = auth()->user()->user_affiliate_type;
              $menu_title = 'Become an Affiliate';
              $show_menu = true;
              $prime_menu = false;
              if (!empty($affiliate_type)) {
                  if ($affiliate_type == App\Models\User::GENERAL) {
                      $menu_title = 'Become Prime Affiliate';
                      $affiliate_route = route('kyc.prime');
                      $show_menu = false;
                  }

                  if ($affiliate_type == App\Models\User::PRIME) {
                      $show_menu = false;
                      $prime_menu = true;
                  }
              }
          @endphp

          <!-- Sidebar Menu -->
          <nav class="mt-2">
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                  data-accordion="false">

                  <!-- Dashboard -->
                  <li class="nav-item">
                      <a href="{{ route('dashboard') }}" class="nav-link">
                          <i class="nav-icon fas fa-tachometer-alt"></i>
                          <p>Dashboard</p>
                      </a>
                  </li>

                  @if ($show_menu)
                      <li class="nav-item">
                          <a href="{{ $affiliate_route }}" class="nav-link">
                              <i class="nav-icon fas fa-handshake"></i>
                              <p>{{ $menu_title }}</p>
                          </a>
                      </li>
                  @endif

                  @if ($prime_menu)
                      <!-- Prime Requests -->
                      <li
                          class="nav-item  {{ menuOpen(['prime_requests','general_affiliates', 'approved_prime_requests', 'rejected_prime_requests']) ? 'menu-is-opening menu-open' : '' }}">
                          <a href="#" class="nav-link">
                              <i class="nav-icon fas fa-user-shield"></i>
                              <p>Prime Operation <i class="right fas fa-angle-left"></i></p>
                          </a>
                          <ul class="nav nav-treeview">
                              <li class="nav-item">
                                  <a href="{{ route('prime_requests') }}"
                                      class="nav-link  {{ request()->routeIs('prime_requests') ? 'active' : '' }}">
                                      <i class="fas fa-hourglass-half nav-icon"></i>
                                      <p>New Requests</p>
                                  </a>
                              </li>
                              {{-- Should be parent item --}}
                              <li class="nav-item">
                                  <a href="{{ route('approved_prime_requests') }}"
                                      class="nav-link  {{ request()->routeIs('approved_prime_requests') ? 'active' : '' }}">
                                      <i class="fas fa-check-circle nav-icon"></i>
                                      <p>Leads Centre</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('rejected_prime_requests') }}"
                                      class="nav-link  {{ request()->routeIs('rejected_prime_requests') ? 'active' : '' }}">
                                      <i class="fas fa-times-circle nav-icon"></i>
                                      <p>Rejected Requests</p>
                                  </a>
                              </li>

                              <li class="nav-item">
                                  <a href="{{ route('general_affiliates') }}"
                                      class="nav-link  {{ request()->routeIs('general_affiliates') ? 'active' : '' }}">
                                      <i class="fas fa-hands-helping nav-icon"></i>
                                      <p>General Affiliates</p>
                                  </a>
                              </li>
                          </ul>
                      </li>

                      <!-- Purchase Management -->
                      <li
                          class="nav-item {{ menuOpen(['products', 'prime_orders', 'prime_order_details', 'used_product', 'sold_product']) ? 'menu-is-opening menu-open' : '' }}">
                          <a href="#" class="nav-link">
                              <i class="nav-icon fas fa-shopping-cart"></i>
                              <p>Purchase Management <i class="right fas fa-angle-left"></i></p>
                          </a>
                          <ul class="nav nav-treeview">
                              <li class="nav-item">
                                  <a href="{{ route('products') }}"
                                      class="nav-link {{ request()->routeIs('products') ? 'active' : '' }}">
                                      <i class="fas fa-box-open nav-icon"></i>
                                      <p>Purchase</p>
                                  </a>
                              </li>
                              <!-- My Orders -->
                              <li class="nav-item">
                                  <a href="{{ route('prime_orders') }}"
                                      class="nav-link {{ request()->routeIs('prime_orders', 'prime_order_details') ? 'active' : '' }}">
                                      <i class="fas fa-receipt nav-icon"></i>
                                      <p>My Orders</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('stock_sale_log') }}" class="nav-link">
                                      <i class="fas fa-history nav-icon"></i>
                                      <p>Sale Logs</p>
                                  </a>
                              </li>
                          </ul>
                      </li>

                      <!-- Inventory -->
                      <li
                          class="nav-item {{ menuOpen(['prime_stock', 'used_product', 'sold_product', 'user.list', 'stock_sale_log']) ? 'menu-is-opening menu-open' : '' }}">
                          <a href="#" class="nav-link">
                              <i class="nav-icon fas fa-warehouse"></i>
                              <p>
                                  Inventory
                                  <i class="right fas fa-angle-left"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">
                              <!-- My Stock -->
                              <li class="nav-item">
                                  <a href="{{ route('prime_stock') }}"
                                      class="nav-link {{ request()->routeIs('prime_stock') ? 'active' : '' }}">
                                      <i class="fas fa-truck nav-icon"></i>
                                      <p>My Stock</p>
                                  </a>
                              </li>

                              <!-- Used Product -->
                              <li class="nav-item">
                                  <a href="{{ route('used_product') }}"
                                      class="nav-link {{ request()->routeIs('used_product') ? 'active' : '' }}">
                                      <i class="fas fa-cash-register nav-icon"></i>
                                      <p>Used Product</p>
                                  </a>
                              </li>

                              <!-- Sold Product -->
                              <li class="nav-item">
                                  <a href="{{ route('sold_product') }}"
                                      class="nav-link {{ request()->routeIs('sold_product') ? 'active' : '' }}">
                                      <i class="fas fa-cash-register nav-icon"></i>
                                      <p>Sold Product</p>
                                  </a>
                              </li>

                              <!-- Product Transfer -->
                              <li class="nav-item">
                                  <a href="#"
                                      class="nav-link {{ request()->routeIs('user.list') ? 'active' : '' }}">
                                      <i class="fas fa-exchange-alt nav-icon"></i>
                                      <p>Product Transfer</p>
                                  </a>
                              </li>
                          </ul>
                      </li>


                      <!-- Accounts -->
                      <li class="nav-item">
                          <a href="#" class="nav-link">
                              <i class="nav-icon fas fa-file-invoice-dollar"></i>
                              <p>Accounts <i class="right fas fa-angle-left"></i></p>
                          </a>
                          <ul class="nav nav-treeview">
                              <li class="nav-item">
                                  <a href="{{ route('user.list') }}" class="nav-link">
                                      <i class="fas fa-wallet nav-icon"></i>
                                      <p>Affiliate Wallet</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('prime_transactions') }}" class="nav-link">
                                      <i class="fas fa-piggy-bank nav-icon"></i>
                                      <p>Prime Wallet</p>
                                  </a>
                              </li>
                          </ul>
                      </li>
                  @endif
                  @if (auth()->user()->user_affiliate_type != App\Models\User::PRIME)
                      <li class="nav-item md:hidden mt-auto">
                          <a href="#" class="nav-link">
                              <p>
                                  <a href="{{ route('kyc.prime') }}"
                                      class="btn bg-yellow-500 px-4 rounded-3xl btn-shine border-shine text-white font-semibold text-md flex gap-2 justify-center items-center hover:opacity-90 focus:ring-2">
                                      <span>Go Prime</span>
                                  </a>
                              </p>
                          </a>
                      </li>
                  @endif
              </ul>
          </nav>

          <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
  </aside>
