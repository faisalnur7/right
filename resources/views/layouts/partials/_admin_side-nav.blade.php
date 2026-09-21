<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Right BD</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Product Management -->
                <li
                    class="nav-item {{ menuOpen(['product.*', 'category.*', 'subcategory.*', 'attribute.*', 'brand.*', 'sale_log.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>
                            Product Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('product.list') }}"
                                class="nav-link {{ request()->routeIs('product.*') ? 'active' : '' }}">
                                <i class="fas fa-box nav-icon"></i>
                                <p>Products</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('category.list') }}"
                                class="nav-link {{ request()->routeIs('category.*') ? 'active' : '' }}">
                                <i class="fas fa-layer-group nav-icon"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('subcategory.list') }}"
                                class="nav-link {{ request()->routeIs('subcategory.*') ? 'active' : '' }}">
                                <i class="fas fa-th-large nav-icon"></i>
                                <p>Sub Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('attribute.list') }}"
                                class="nav-link {{ request()->routeIs('attribute.*') ? 'active' : '' }}">
                                <i class="fas fa-sliders-h nav-icon"></i>
                                <p>Attributes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('brand.list') }}"
                                class="nav-link {{ request()->routeIs('brand.*') ? 'active' : '' }}">
                                <i class="fas fa-tags nav-icon"></i>
                                <p>Brand</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('sale_log.list') }}"
                                class="nav-link {{ request()->routeIs('sale_log.*') ? 'active' : '' }}">
                                <i class="fas fa-receipt nav-icon"></i>
                                <p>Sale Log</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Purchase Management -->
                <li class="nav-item {{ menuOpen(['supplier.*', 'purchase.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Purchase Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('purchase.create') }}"
                                class="nav-link {{ request()->routeIs('purchase.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-square nav-icon"></i>
                                <p>Purchase Product</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('purchase.list') }}"
                                class="nav-link {{ request()->routeIs('purchase.list') ? 'active' : '' }}">
                                <i class="fas fa-history nav-icon"></i>
                                <p>Purchase History</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('supplier.list') }}"
                                class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                                <i class="fas fa-industry nav-icon"></i>
                                <p>Suppliers</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Order Management -->
                <li class="nav-item {{ menuOpen(['order.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>
                            Order Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('order.list') }}"
                                class="nav-link {{ request()->routeIs('order.list') ? 'active' : '' }}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Order List</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Shipping Management -->
                <li class="nav-item {{ menuOpen(['shipping_rule.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shipping-fast"></i>
                        <p>
                            Shipping Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('shipping-rule.index') }}"
                                class="nav-link {{ request()->routeIs('shipping_rule.*') ? 'active' : '' }}">
                                <i class="fas fa-truck nav-icon"></i>
                                <p>Shipping Rule</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Subscriptions -->
                <li
                    class="nav-item {{ menuOpen(['subscription_package.*', 'package_feature', 'pending_list', 'admin_reference_request.list']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>
                            Subscriptions
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('pending_list') }}"
                                class="nav-link {{ request()->routeIs('pending_list') ? 'active' : '' }}">
                                <i class="fas fa-clock nav-icon"></i>
                                <p>New Requests</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin_reference_request.list') }}"
                                class="nav-link {{ request()->routeIs('admin_reference_request.list*') ? 'active' : '' }}">
                                <i class="fas fa-user-check nav-icon"></i>
                                <p>Reference Requests</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('subscription_package.list') }}"
                                class="nav-link {{ request()->routeIs('subscription_package.*') ? 'active' : '' }}">
                                <i class="fas fa-box nav-icon"></i>
                                <p>Package</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('package_feature.list') }}"
                                class="nav-link {{ request()->routeIs('package_feature.*') ? 'active' : '' }}">
                                <i class="fas fa-list-ul nav-icon"></i>
                                <p>Package Features</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Leads Management -->
                <li
                    class="nav-item {{ menuOpen(['adminTreeView', 'prime_active_request.list', 'adminUserList']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Leads Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('adminTreeView') }}"
                                class="nav-link {{ request()->routeIs('adminTreeView') ? 'active' : '' }}">
                                <i class="fas fa-sitemap nav-icon"></i>
                                <p>User Tree</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('prime_active_request.list') }}"
                                class="nav-link {{ request()->routeIs('prime_active_request.list*') ? 'active' : '' }}">
                                <i class="fas fa-star nav-icon"></i>
                                <p>Placement Request</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('adminUserList') }}"
                                class="nav-link {{ request()->routeIs('adminUserList') ? 'active' : '' }}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Disbursements -->
                <li
                    class="nav-item {{ menuOpen(['disbursement_list', 'disbursement_list_completed']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-hand-holding-usd"></i>
                        <p>
                            Disbursements
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('disbursement_list') }}"
                                class="nav-link {{ request()->routeIs('disbursement_list') ? 'active' : '' }}">
                                <i class="fas fa-user-clock nav-icon"></i>
                                <p>User list</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Associate Assignment -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>
                            Associate Assignment
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('user.list') }}" class="nav-link">
                                <i class="fas fa-map-marker-alt nav-icon"></i>
                                <p>District Associate</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.list') }}" class="nav-link">
                                <i class="fas fa-map-pin nav-icon"></i>
                                <p>Thana Associate</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.list') }}" class="nav-link">
                                <i class="fas fa-mail-bulk nav-icon"></i>
                                <p>Postal Associate</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- User Management -->
                <li class="nav-item ">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>
                            User Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- <li class="nav-item">
                            <a href="{{ route('adminUserList') }}"
                                class="nav-link {{ request()->routeIs('adminUserList') ? 'active' : '' }}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li> --}}
                    </ul>
                </li>

                <!-- Role Management -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>
                            Role Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('role.list') }}" class="nav-link">
                                <i class="fas fa-user-tag nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Payment Management (NEW) -->
                <li class="nav-item {{ menuOpen(['payment_option.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>
                            Payment Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('payment_option.list') }}"
                                class="nav-link {{ request()->routeIs('payment_option.*') ? 'active' : '' }}">
                                <i class="fas fa-credit-card nav-icon"></i>
                                <p>Payment Options</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Settings -->
                <li
                    class="nav-item {{ menuOpen(['division.*', 'district.*', 'police-station.*', 'post-office.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Settings
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <!-- Location Setting -->
                        <li
                            class="nav-item {{ menuOpen(['division.*', 'district.*', 'police-station.*', 'post-office.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs(['division.*', 'district.*', 'police-station.*', 'post-office.*']) ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-map-marked-alt nav-icon"></i>
                                <p>
                                    Location Setting
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('division.index') }}"
                                        class="nav-link {{ request()->routeIs('division.*') ? 'active' : '' }}">
                                        <i class="fas fa-map nav-icon"></i>
                                        <p>Division</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('district.index') }}"
                                        class="nav-link {{ request()->routeIs('district.*') ? 'active' : '' }}">
                                        <i class="fas fa-map-pin nav-icon"></i>
                                        <p>District</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('police-station.index') }}"
                                        class="nav-link {{ request()->routeIs('police-station.*') ? 'active' : '' }}">
                                        <i class="fas fa-landmark nav-icon"></i>
                                        <p>Police Station</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('post-office.index') }}"
                                        class="nav-link {{ request()->routeIs('post-office.*') ? 'active' : '' }}">
                                        <i class="fas fa-envelope-open-text nav-icon"></i>
                                        <p>Post Office</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.general-settings.edit') }}" class="nav-link">
                                <i class="fas fa-tools nav-icon"></i>
                                <p>Site Settings</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
