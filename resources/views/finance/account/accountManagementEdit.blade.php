@include('layouts.header')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('layouts.nav')
        @include('layouts.sidebar')
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Account Management</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Account Management
                                    </li>
                                </ol>
                            </div>

                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('delete_success'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('delete_success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <section class="content container-fluid">

                    <div class="row justify-content-center mt-3">
                        <div class="col-md-10 ">
                            <div class="card card-color">
                                <div class="card-header  mt-2 mb-2">
                                    <h4 class="cade-title text-center">Account Management Edit Page</h4>
                                </div>
                                <div class="card-body ">
                                    <form action="{{ url('account_update', $account->id) }}" method="POST">
                                        @csrf
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="phno">Location <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="location" required>
                                                    <option value="" selected disabled>Choose Location
                                                    </option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}"
                                                            @if ($branch->id == $account->location) selected @endif>
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="name">Account Number <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="Account_Number"
                                                    placeholder="Enter Name" required autofocus name="account_number"
                                                    value="{{ $account->account_number }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="crc"> Account Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="Account_Name"
                                                    placeholder="Enter Phone Number" name="account_name"
                                                    value="{{ $account->account_name }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="account_name">Type <span
                                                        style="color: red;">&nbsp;*</span></label>
                                                <select name="account_type" id="type" class="form-control">
                                                    <option value="" disabled
                                                        {{ empty($account->account_type) ? 'selected' : '' }}>Select
                                                        Account Type</option>
                                                    <option value="Non Current Assets"
                                                        {{ $account->account_type == 'Non Current Assets' ? 'selected' : '' }}>
                                                        Non Current Assets (BL)</option>
                                                    <option value="Current Assets"
                                                        {{ $account->account_type == 'Current Assets' ? 'selected' : '' }}>
                                                        Current Assets (BL)</option>
                                                    <option value="Long Term Liability"
                                                        {{ $account->account_type == 'Long Term Liability' ? 'selected' : '' }}>
                                                        Long Term Liability (BL)</option>
                                                    <option value="Current Liability"
                                                        {{ $account->account_type == 'Current Liability' ? 'selected' : '' }}>
                                                        Current Liability (BL)</option>
                                                    <option value="Non Current Liability"
                                                        {{ $account->account_type == 'Non Current Liability' ? 'selected' : '' }}>
                                                        Non Current Liability (BL)</option>
                                                    <option value="Equity"
                                                        {{ $account->account_type == 'Equity' ? 'selected' : '' }}>
                                                        Equity (BL)</option>
                                                    <option value="Revenue"
                                                        {{ $account->account_type == 'Revenue' ? 'selected' : '' }}>
                                                        Revenue (PL)</option>
                                                    <option value="Cost of Sale"
                                                        {{ $account->account_type == 'Cost of Sale' ? 'selected' : '' }}>
                                                        Cost of Sale (PL)</option>
                                                    <option value="Other income"
                                                        {{ $account->account_type == 'Other income' ? 'selected' : '' }}>
                                                        Other income (PL)</option>
                                                    <option value="Admin Expenses"
                                                        {{ $account->account_type == 'Admin Expenses' ? 'selected' : '' }}>
                                                        Admin Expenses (PL)</option>
                                                    <option value="Depreciation"
                                                        {{ $account->account_type == 'Depreciation' ? 'selected' : '' }}>
                                                        Depreciation (PL)</option>
                                                    <option value="Mainteance Expenses"
                                                        {{ $account->account_type == 'Mainteance Expenses' ? 'selected' : '' }}>
                                                        Mainteance Expenses (PL)</option>
                                                    <option value="S & D Expenses"
                                                        {{ $account->account_type == 'S & D Expenses' ? 'selected' : '' }}>
                                                        S & D Expenses (PL)</option>
                                                    <option value="Other Expenses"
                                                        {{ $account->account_type == 'Other Expenses' ? 'selected' : '' }}>
                                                        Other Expenses (PL)</option>
                                                    <option value="Marketing Expenses"
                                                        {{ $account->account_type == 'Marketing Expenses' ? 'selected' : '' }}>
                                                        Marketing Expenses (PL)</option>
                                                    <option value="Finance Expenses"
                                                        {{ $account->account_type == 'Finance Expenses' ? 'selected' : '' }}>
                                                        Finance Expenses (PL)</option>
                                                </select>
                                                <input type="hidden" name="account_bl_pl" id="bl_pl"
                                                    value="{{ $account->account_bl_pl }}">
                                                @error('type')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class=" justify-content-between">
                                                <a href="{{ route('finance#accountManagement') }}"><button
                                                        type="button" class="btn btn-dark">Back</button></a>
                                                <button type="submit" class="btn btn-primary">Update </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- Modal End --}}
                </section>


            </section>

        </div>



    </div>




    <script>
        const homeRadio = document.getElementById('home');
        const bankRadio = document.getElementById('bank');
        const wavepayRadio = document.getElementById('wavepay');
        const kpayRadio = document.getElementById('kpay');
        const bankCard = document.getElementById('bankCard');
        const wavepayCard = document.getElementById('wavepayCard');
        const kpayCard = document.getElementById('kpayCard');

        const bankNameInput = document.getElementById('bankName');
        const bankNumberInput = document.getElementById('bankAccountNumber');
        const payNameInput = document.getElementById('accountName');
        const payPhoneInput = document.getElementById('phoneNumber');

        const accountNamewave = document.getElementById('accountNamewave');
        const phoneNumberwave = document.getElementById('phoneNumberwave');

        // Function to hide all cards
        function hideAllCards() {
            bankCard.style.display = 'none';
            kpayCard.style.display = 'none';
            wavepayCard.style.display = 'none';
        }

        // Add event listeners to radio buttons
        homeRadio.addEventListener('change', function() {
            if (this.checked) {
                hideAllCards();
            }
        });

        // Function to hide all cards
        if (bankRadio.checked) {
            //const bankCard = document.getElementById('bankCard');
            bankCard.style.display = 'block';
        }
        if (kpayRadio.checked) {
            //const kpayCard = document.getElementById('kpayCard');
            kpayCard.style.display = 'block';
        }
        if (wavepayRadio.checked) {
            // const kpayCard = document.getElementById('wavepayCard');
            wavepayCard.style.display = 'block';
        }

        bankRadio.addEventListener('change', function() {
            if (this.checked) {
                bankCard.style.display = 'block';
                wavepayCard.style.display = 'none';
                kpayCard.style.display = 'none';
                payNameInput.value = '';
                payPhoneInput.value = '';
                bankNameInput.value = '';
                bankNumberInput.value = '';

                accountNamewave.value = '';
                phoneNumberwave.value = '';
            }
        });

        kpayRadio.addEventListener('change', function() {
            if (this.checked) {
                kpayCard.style.display = 'block';
                bankCard.style.display = 'none';
                wavepayCard.style.display = 'none';
                bankNameInput.value = '';
                bankNumberInput.value = '';
                payNameInput.value = '';
                payPhoneInput.value = '';
            }
        });

        wavepayRadio.addEventListener('change', function() {
            if (this.checked) {
                wavepayCard.style.display = 'block';
                kpayCard.style.display = 'none';
                bankCard.style.display = 'none';
                bankNameInput.value = '';
                bankNumberInput.value = '';
                payNameInput.value = '';
                payPhoneInput.value = '';
            }
        });
    </script>

    <script>
        document.getElementById('type').addEventListener('change', function() {
            var selectedValue = this.value;
            var hiddenInput = document.getElementById('bl_pl');

            var blCategories = [
                'Non Current Assets',
                'Current Assets',
                'Long Term Liability',
                'Current Liability',
                'Non Current Liability',
                'Equity'
            ];

            var plCategories = [
                'Revenue',
                'Cost of Sale',
                'Other income',
                'Admin Expenses',
                'Depreciation',
                'Mainteance Expenses',
                'S & D Expenses',
                'Other Expenses',
                'Marketing Expenses',
                'Finance Expenses'
            ];

            if (blCategories.includes(selectedValue)) {
                hiddenInput.value = 'BL';
            } else if (plCategories.includes(selectedValue)) {
                hiddenInput.value = 'PL';
            } else {
                hiddenInput.value = '';
            }
        });
    </script>

    @include('layouts.footer')
