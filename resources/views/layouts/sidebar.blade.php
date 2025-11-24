<style>
    .main-sidebar {
        /* background: radial-gradient(circle, #4549aa, #d1413d); */
        background: #417EC1;
        font-family:
            "Times New Roman", serif;

    }

    .nav-link {
        background-color: transparent;
        /* Default background */
        transition: background-color 0.3s, color 0.3s;
        /* Smooth transition */
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2);

    }
</style>
<aside class="main-sidebar sidebar-primary elevation-4">
    <!-- Brand Logo -->
    <span class="text-center brand-link ">
        <span class="text-white brand-text font-weight-bold">POS</span>
    </span>


    <!-- Sidebar -->
    <div class="sidebar ">


        <!-- Sidebar Menu -->
        <nav class="my-2">
            <ul class="nav nav-pills nav-sidebar flex-column " data-widget="treeview" role="menu" data-accordion="false">
                @php
                    $userPermissions = [];
                    if (auth()->user()->permission) {
                        $decodedPermissions = json_decode(auth()->user()->permission, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $userPermissions = $decodedPermissions;
                        }
                    }
                @endphp


                @if (in_array('Customer', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="text-white fa-solid fa-user-plus nav-icon"></i>
                            <p class="pl-3 text-white">
                                Invoices
                                <i class="text-white right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('customer') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Customer List</p>
                                </a>
                            </li>


                        </ul>
                    </li>

                @endif




            </ul>
        </nav>


    </div>
</aside>
