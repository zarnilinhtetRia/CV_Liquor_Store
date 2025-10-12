<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Dashboard</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<style>
    body {
        font-family: "Times New Roman", serif;
    }

    .col-md-4:hover {
        transform: scale(1.05);
        /* Slight zoom effect */
        transition: transform 0.3s ease;
        /* Smooth transition */
    }
</style>

<body class="bg-light">
    <nav class="main-header navbar navbar-expand-lg navbar-dark px-3 py-2 shadow" style="background-color: #092366">
        <!-- Left navbar links -->
        <div class="navbar-nav d-flex align-items-center">
            <a class="nav-link text-white" href="#">Date - <?= date('d-m-y') ?></a>
        </div>

        <!-- Right navbar links -->
        <div class="ms-auto">
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user"></i> {{ auth()->user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container mt-5">
        <div class="row g-4">
            @php
                $userPermissions = [];
                if (auth()->user()->permission) {
                    $decodedPermissions = json_decode(auth()->user()->permission, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $userPermissions = $decodedPermissions;
                    }
                }
            @endphp

            <!-- Dashboard -->
            <div class="col-md-4 col-4">
                <a href="{{ url('dashboard') }}" style="text-decoration: none;">
                    <div
                        class="bg-primary text-white p-4 rounded-4 d-flex flex-column align-items-center justify-content-center shadow-lg">
                        <i class="fas fa-home display-4 p-3 bg-light text-primary rounded-circle"></i>
                        <h4 class="mt-2">Dashboard</h4>
                    </div>
                </a>
            </div>

            @if (in_array('Item', $userPermissions) || auth()->user()->is_admin == '1')
                <div class="col-md-4 col-4">
                    <a href="{{ url('items') }}" style="text-decoration: none;">
                        <div
                            class="bg-danger text-white p-4 rounded-4 d-flex flex-column align-items-center justify-content-center shadow-lg">
                            <i class="fas fa-boxes display-4 p-3 bg-light text-danger rounded-circle"></i>
                            <h4 class="mt-2">Product</h4>
                        </div>
                    </a>
                </div>
            @endif

            @if (in_array('POS', $userPermissions) || auth()->user()->is_admin == '1')
                <div class="col-md-4 col-4">
                    <a href="{{ url('pos') }}" style="text-decoration: none;">
                        <div
                            class="bg-success text-white p-4 rounded-4 d-flex flex-column align-items-center justify-content-center shadow-lg">
                            <i class="fas fa-cash-register display-4 p-3 bg-light text-success rounded-circle"></i>
                            <h4 class="mt-2">POS</h4>
                        </div>
                    </a>
                </div>
            @endif

            @if (in_array('Invoice', $userPermissions) || auth()->user()->is_admin == '1')
                <div class="col-md-4 col-4">
                    <a href="{{ url('invoice') }}" style="text-decoration: none;">
                        <div
                            class="bg-warning text-dark p-4 rounded-4 d-flex flex-column align-items-center justify-content-center shadow-lg">
                            <i
                                class="fas fa-file-invoice-dollar display-4 p-3 bg-light text-warning rounded-circle"></i>
                            <h4 class="mt-2">Invoice</h4>
                        </div>
                    </a>
                </div>
            @endif

            @if (in_array('Purchase Order', $userPermissions) || auth()->user()->is_admin == '1')
                <div class="col-md-4 col-4">
                    <a href="{{ url('purchase_order_manage') }}" style="text-decoration: none;">
                        <div
                            class="bg-info text-white p-4 rounded-4 d-flex flex-column align-items-center justify-content-center shadow-lg">
                            <i class="fas fa-shopping-cart display-4 p-3 bg-light text-info rounded-circle"></i>
                            <h4 class="mt-2">PO</h4>
                        </div>
                    </a>
                </div>
            @endif

            @if (in_array('Transfer', $userPermissions) || auth()->user()->is_admin == '1')
                <div class="col-md-4 col-4">
                    <a href="{{ url('show_transfer_history') }}" style="text-decoration: none;">
                        <div
                            class="bg-secondary text-white p-4 rounded-4 d-flex flex-column align-items-center justify-content-center shadow-lg">
                            <i class="fas fa-exchange-alt display-4 p-3 bg-light text-secondary rounded-circle"></i>
                            <h4 class="mt-2">Transfer</h4>
                        </div>
                    </a>
                </div>
            @endif

            @if (in_array('Expenses', $userPermissions) || auth()->user()->is_admin == '1')
                <div class="col-md-4 col-4">
                    <a href="{{ url('expense') }}" style="text-decoration: none;">
                        <div
                            class="bg-dark text-white p-4 rounded-4 d-flex flex-column align-items-center justify-content-center shadow-lg">
                            <i class="fas fa-wallet display-4 p-3 bg-light text-secondary rounded-circle"></i>
                            <h4 class="mt-2">Expenses</h4>
                        </div>
                    </a>
                </div>
            @endif

            @if (in_array('Account', $userPermissions) || auth()->user()->is_admin == '1')
                <div class="col-md-4 col-4">
                    <a href="{{ url('accountManagement') }}" style="text-decoration: none;">
                        <div
                            class="bg-primary text-white p-4 rounded-4 d-flex flex-column align-items-center justify-content-center shadow-lg">
                            <i class="fas fa-calculator display-4 p-3 bg-light text-dark rounded-circle"></i>
                            <h4 class="mt-2">Accounting</h4>
                        </div>
                    </a>
                </div>
            @endif

            @if (in_array('Customer', $userPermissions) || auth()->user()->is_admin == '1')
                <div class="col-md-4 col-4">
                    <a href="{{ url('customer') }}" style="text-decoration: none;">
                        <div
                            class="bg-purple text-white p-4 rounded-4 d-flex flex-column align-items-center justify-content-center shadow-lg">
                            <i class="fas fa-users display-4 p-3 bg-light text-success rounded-circle"></i>
                            <h4 class="mt-2 text-dark">Customer</h4>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
