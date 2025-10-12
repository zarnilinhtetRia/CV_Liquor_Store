@include('layouts.header')
<style>
    #tableSelect {
        background-color: #6c757d;
        color: white;
    }

    #tableSelect option {
        background-color: white;
        color: black;
    }


    #tableSelect:focus {
        background-color: #6c757d;
        color: white;
    }
</style>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('layouts.nav')
        @include('layouts.sidebar')
        <div class="content-wrapper">


            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">


                            <!-- Content Header (Page header) -->
                            <section class="content-header">
                                <div class="container-fluid">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <h1>Transaction Add Payment</h1>
                                        </div>
                                        <div class="col-sm-6">
                                            <ol class="breadcrumb float-sm-right">
                                                <li class="breadcrumb-item"><a
                                                        href="{{ url('/dashboard') }}">Dashboard</a></li>
                                                <li class="breadcrumb-item active">Transaction Add Payment</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div><!-- /.container-fluid -->
                            </section>
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if (session('deleteStatus'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {{ session('deleteStatus') }}
                                </div>
                            @endif
                            @if (session('updateStatus'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {{ session('updateStatus') }}
                                </div>
                            @endif

                            {{-- Permission --}}
                            @php
                                $choosePermission = [];
                                if (auth()->user()->permission) {
                                    $decodedPermissions = json_decode(auth()->user()->permission, true);
                                    if (json_last_error() === JSON_ERROR_NONE) {
                                        $choosePermission = $decodedPermissions;
                                    }
                                }
                            @endphp

                            {{-- End Permission --}}
                            <div class="container-fluid mb-4 mr-auto">
                                <div class="row justify-content-start align-items-start">
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-default text-white" data-toggle="modal"
                                            data-target="#modal-lg" style="background-color: #007BFF;">
                                            Payment Register
                                        </button>
                                    </div>

                                    @if (in_array('Opening Balance', $choosePermission) || auth()->user()->is_admin == '1')
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-success text-white"
                                                data-toggle="modal" data-target="#modal-opening">
                                                Opening Balance
                                            </button>
                                        </div>
                                    @endif

                                    @if (in_array('Payment Delete Record', $choosePermission) || auth()->user()->is_admin == '1')
                                        <div class="col-auto">
                                            <a href="{{ url('delete_record_payment', $transaction->id) }}"
                                                class="btn btn-danger text-white">
                                                Payment Delete Record
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>


                            {{-- @dd($transaction); --}}
                            <h5 class="my-3"> Transaction
                                Name -{{ $transaction->transaction_name }}</h5>
                            @php
                                $groupedPayments = $add_payments->groupBy('transaction_id');
                            @endphp

                            {{-- @foreach ($groupedPayments as $transactionId => $payments) --}}
                            @php
                                $sumInAmount = $add_payments->where('payment_status', 'IN')->sum(function ($payment) {
                                    return (float) $payment->amount; // Cast to float
                                });

                                $sumInAmount += $makepayment
                                    ->where('invoice_no', '!=', 'Customer Return')
                                    ->sum(function ($payment) {
                                        return (float) $payment->amount; // Cast to float
                                    });

                                $sumInAmount += $invoice_receive
                                    ->where('quote_no', '!=', 'Customer Return')
                                    ->sum(function ($payment) {
                                        return (float) $payment->payment_amount; // Cast to float
                                    });

                                $sumInAmount += $invoice_sale
                                    ->where('quote_no', '!=', 'Customer Return')
                                    ->sum(function ($payment) {
                                        return (float) $payment->payment_amount; // Cast to float
                                    });

                                $sumOutAmount = $add_payments->where('payment_status', 'OUT')->sum(function ($payment) {
                                    return (float) $payment->amount; // Cast to float
                                });

                                $sumOutAmount += $invoice_receive
                                    ->where('quote_no', '==', 'Customer Return')
                                    ->sum(function ($payment) {
                                        return (float) $payment->payment_amount; // Cast to float
                                    });
                                $sumOutAmount += $invoice_sale
                                    ->where('quote_no', '==', 'Customer Return')
                                    ->sum(function ($payment) {
                                        return (float) $payment->payment_amount; // Cast to float
                                    });

                                $sumOutAmount += $purchasePayment->sum(function ($payment) {
                                    return (float) $payment->payment_amount; // Cast to float
                                });
                                $sumOutAmount += $makepayment
                                    ->where('invoice_no', 'Customer Return')
                                    ->sum(function ($payment) {
                                        return (float) $payment->amount; // Cast to float
                                    });
                                // $sumOutAmount += $customer_return_invoices->sum(function ($payment) {
                                //     return (float) $payment->payment_amount; // Cast to float
                                // });

                                // dd($sumOutAmount);

                                $sumOutAmount += $expenses->sum(function ($expense) {
                                    return (float) $expense->amount_mmk; // Cast to float
                                });

                            @endphp

                            <h5 class="mb-4">Amount: {{ $sumInAmount - $sumOutAmount }}</h5>
                            {{-- @endforeach --}}


                            {{-- Opening Modal --}}
                            <div class="modal fade" id="modal-opening">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Opening Balance Register</h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ url('/transaction_payment_register', $transaction->id) }}"
                                                method="POST">
                                                @csrf
                                                <input type="text" value="Opening Balance " name="opening_balance"
                                                    id="opening_balance" class="form-control" hidden>
                                                <input type="hidden" value="{{ $transaction->id }}"
                                                    name="transaction_id">
                                                <input type="hidden" value="{{ $transaction->location }}"
                                                    id="location">
                                                <input type="hidden" value="{{ $transaction->account->id }}"
                                                    name="account_id">
                                                <div class="form-group">
                                                    <label for="debit_note">Voucher No. <span
                                                            style="color: red;">*</span></label>
                                                    <input type="text" class="form-control" name="debit_note"
                                                        placeholder="Enter Voucher No" required>
                                                    @error('debit_note')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="status">Status <span
                                                            style="color: red;">*</span></label>
                                                    <select name="payment_status" class="form-control" id="status"
                                                        required>
                                                        <option value="">Choose One</option>
                                                        <option value="IN">IN</option>
                                                        <option value="OUT">OUT</option>
                                                    </select>
                                                    @error('status')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                {{-- <div class="form-group">
                                                    <label for="status">Account</label>
                                                    <select name="transfer_account_id" class="form-control"
                                                        id="transfer_account_id">
                                                        <option value="">Choose Select Account</option>
                                                        @foreach ($accounts as $account)
                                                            <option value="{{ $account->id }}">
                                                                {{ $account->account_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('status')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div> --}}

                                                <div class="form-group">
                                                    <label for="receiver_name">Receiver Name <span
                                                            style="color: red;">*</span></label>
                                                    <input type="text" class="form-control" id="receiver_name"
                                                        name="receiver_name" placeholder="Enter Receiver Name"
                                                        required>
                                                    @error('receiver_name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="reference_no">Reference No. <span
                                                            style="color: red;">*</span></label>
                                                    <input type="text" class="form-control" id="reference_no"
                                                        name="reference_no" placeholder="Enter reference no" required>
                                                    @error('reference_no')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="amount">Amount <span
                                                            style="color: red;">*</span></label>
                                                    <input type="number" class="form-control" id="amount"
                                                        name="amount" placeholder="Enter Amount" required>
                                                    @error('transaction_code')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="payment_date">Payment Date <span
                                                            style="color: red;">*</span></label>
                                                    <input type="date" class="form-control" id="payment_date"
                                                        name="payment_date" required>
                                                    @error('payment_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="note">Description</label>
                                                    <textarea class="form-control" id="note" name="note" rows="3" placeholder="Enter ..."></textarea>
                                                    @error('note')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="modal-footer justify-content-between">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary"
                                                        style="background-color: #007BFF">Register</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>



                            {{-- Modal Content --}}
                            <div class="modal fade" id="modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Payment Register</h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form
                                                action="{{ url('/transaction_payment_register', $transaction->id) }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" value="{{ $transaction->id }}"
                                                    name="transaction_id">
                                                <input type="hidden" value="{{ $transaction->location }}"
                                                    id="location">
                                                <input type="hidden" value="{{ $transaction->account->id }}"
                                                    name="account_id">
                                                <div class="form-group">
                                                    <label for="debit_note">Voucher No. <span
                                                            style="color: red;">*</span></label>
                                                    <input type="text" class="form-control" id="voucher_no"
                                                        name="debit_note" placeholder="Enter Voucher No" required
                                                        readonly>
                                                    @error('debit_note')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="status">Status <span
                                                            style="color: red;">*</span></label>
                                                    <select name="payment_status" class="form-control" id="status"
                                                        required>
                                                        <option value="">Choose One</option>
                                                        <option value="IN">IN</option>
                                                        <option value="OUT">OUT</option>
                                                    </select>
                                                    @error('status')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="status">Account</label>
                                                    <select name="transfer_account_id" class="form-control"
                                                        id="transfer_account_id">
                                                        <option value="">Choose Select Account</option>
                                                        @foreach ($accounts as $account)
                                                            <option value="{{ $account->id }}">
                                                                {{ $account->account_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('status')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="receiver_name">Receiver Name <span
                                                            style="color: red;">*</span></label>
                                                    <input type="text" class="form-control" id="receiver_name"
                                                        name="receiver_name" placeholder="Enter Receiver Name"
                                                        required>
                                                    @error('receiver_name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="reference_no">Reference No. <span
                                                            style="color: red;">*</span></label>
                                                    <input type="text" class="form-control" id="reference_no"
                                                        name="reference_no" placeholder="Enter reference no" required>
                                                    @error('reference_no')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="amount">Amount <span
                                                            style="color: red;">*</span></label>
                                                    <input type="number" class="form-control" id="amount"
                                                        name="amount" placeholder="Enter Amount" required>
                                                    @error('transaction_code')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="payment_date">Payment Date <span
                                                            style="color: red;">*</span></label>
                                                    <input type="date" class="form-control" id="payment_date"
                                                        name="payment_date" required>
                                                    @error('payment_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="note">Description</label>
                                                    <textarea class="form-control" id="note" name="note" rows="3" placeholder="Enter ..."></textarea>
                                                    @error('note')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="modal-footer justify-content-between">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary"
                                                        style="background-color: #007BFF">Register</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>



                            <!-- Nav tabs -->

                            <div class="col-md-3 col-6 col-lg-2 mb-5">
                                <select
                                    class="form-control shadow-sm p-2 bg-secondary text-white border-0 rounded-pill"
                                    id="tableSelect">
                                    <option value="payment" selected>Payment Table</option>
                                    <option value="invoice">Invoice Table</option>

                                    <option value="po">PO Table</option>
                                    <option value="pr">PO Return Table</option>
                                    <option value="sr-inv">Sale Return (Invoice) Table</option>
                                    <option value="expense">Expense Table</option>
                                    <option value="customer_return">Customer Return Table</option>

                                </select>
                            </div>



                            <!-- Tab content -->
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="payment" role="tabpanel"
                                    aria-labelledby="payment-tab">
                                    <div class="card">

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="example1" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            {{-- <th>No</th>
                                                            <th>Account Name</th>
                                                            <th>Status</th>
                                                            <th>Amount</th>
                                                            <th>Voucher No</th>
                                                            <th>Receiver Name</th>
                                                            <th>Reference No</th>
                                                            <th>Description</th>
                                                            <th>Action</th> --}}
                                                            <th>No</th>
                                                            <th>Account Name</th>
                                                            <th>Customer / Supplier Name</th>
                                                            <th>Status</th>
                                                            <th>Voucher No</th>
                                                            <th>Description</th>
                                                            <th>Date</th>
                                                            <th>Amount</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $no = '1';
                                                            $total = 0;
                                                            $totalCR = 0;
                                                            $totalInv = 0;
                                                        @endphp
                                                        @foreach ($makepayment as $payment)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $payment->transaction->account->account_name }}
                                                                </td>
                                                                <td>{{ $payment->Id_invoice->customer ? $payment->Id_invoice->customer->name : '' }}
                                                                </td>
                                                                <td>
                                                                    @if (
                                                                        ($payment->invoice ? $payment->invoice->balance_due : $payment->Id_invoice->balance_due) == 'Invoice' &&
                                                                            $payment->Id_invoice->status == 'invoice')
                                                                        In (Invoice)

                                                                        @php
                                                                            $totalInv += $payment->amount;
                                                                        @endphp
                                                                    @elseif ($payment->Id_invoice->status == 'customer return')
                                                                        Out (Customer Return)

                                                                        @php
                                                                            $totalCR += $payment->amount;
                                                                        @endphp
                                                                    @elseif ($payment->Id_invoice->status == 'pos')
                                                                        POS
                                                                        @php
                                                                            $totalInv += $payment->amount;
                                                                        @endphp
                                                                    @else
                                                                        In (PO Return)
                                                                    @endif
                                                                </td>
                                                                <td>{{ $payment->invoice_no ?? 'Not Voucher' }}
                                                                </td>
                                                                <td>{{ $payment->invoice ? $payment->invoice->remark : $payment->Id_invoice->remark }}
                                                                </td>
                                                                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                                                                </td>

                                                                <td>{{ $payment->amount }}</td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                                $total = $totalInv - $totalCR;

                                                            @endphp
                                                        @endforeach
                                                        @foreach ($invoice_receive as $payment)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $payment->transaction->account->account_name }}
                                                                </td>
                                                                <td>{{ $payment->invoice->customer ? $payment->invoice->customer->name : '' }}
                                                                </td>
                                                                <td>
                                                                    @if (
                                                                        ($payment->invoice ? $payment->invoice->balance_due : $payment->Id_invoice->balance_due) == 'Invoice' &&
                                                                            ($payment->invoice ? $payment->invoice->status : $payment->Id_invoice->status) == 'invoice')
                                                                        Receivable (Invoice)

                                                                        @php
                                                                            $totalInv += $payment->payment_amount;
                                                                        @endphp
                                                                    @elseif (
                                                                        ($payment->invoice ? $payment->invoice->balance_due : $payment->Id_invoice->balance_due) == 'Invoice' &&
                                                                            ($payment->invoice ? $payment->invoice->status : $payment->Id_invoice->status) == 'customer return')
                                                                        Customer Return

                                                                        @php
                                                                            $totalCR += $payment->payment_amount;
                                                                        @endphp
                                                                    @else
                                                                        Receivable (PO Return)
                                                                    @endif
                                                                </td>

                                                                <td>{{ $payment->invoice ? $payment->invoice->invoice_no : $payment->Id_invoice->invoice_no }}
                                                                </td>
                                                                <td>{{ $payment->invoice ? $payment->invoice->remark : $payment->Id_invoice->remark }}
                                                                </td>
                                                                <td>{{ \Carbon\Carbon::parse($payment->invoice->invoice_date)->format('d/m/Y') }}
                                                                </td>
                                                                <td>{{ $payment->payment_amount }}</td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                                $total = $totalInv - $totalCR;
                                                            @endphp
                                                        @endforeach

                                                        @foreach ($invoice_sale as $payment)
                                                            <tr>

                                                                <td>{{ $no }}</td>
                                                                <td>{{ $payment->transaction->account->account_name }}
                                                                </td>
                                                                <td>{{ $payment->invoice->customer ? $payment->invoice->customer->name : '' }}
                                                                </td>
                                                                <td>
                                                                    @if (
                                                                        ($payment->invoice ? $payment->invoice->balance_due : $payment->Id_invoice->balance_due) == 'Invoice' &&
                                                                            ($payment->invoice ? $payment->invoice->status : $payment->Id_invoice->status) == 'invoice')
                                                                        Sale Account (Invoice)
                                                                        @php
                                                                            $totalInv += $payment->payment_amount;
                                                                        @endphp
                                                                    @elseif (
                                                                        ($payment->invoice ? $payment->invoice->balance_due : $payment->Id_invoice->balance_due) == 'Invoice' &&
                                                                            ($payment->invoice ? $payment->invoice->status : $payment->Id_invoice->status) == 'customer return')
                                                                        Customer Return
                                                                        @php
                                                                            $totalCR += $payment->payment_amount;
                                                                        @endphp
                                                                    @else
                                                                    @endif
                                                                </td>

                                                                <td>{{ $payment->invoice ? $payment->invoice->invoice_no : $payment->Id_invoice->invoice_no }}
                                                                </td>
                                                                <td>{{ $payment->invoice ? $payment->invoice->remark : $payment->Id_invoice->remark }}
                                                                </td>
                                                                <td>{{ \Carbon\Carbon::parse($payment->invoice->invoice_date)->format('d/m/Y') }}
                                                                </td>
                                                                <td>{{ $payment->payment_amount }}</td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                                $total = $totalInv - $totalCR;

                                                            @endphp
                                                        @endforeach



                                                        @foreach ($purchasePayment as $payment)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $payment->transaction->account->account_name }}
                                                                </td>
                                                                <td>{{ $payment->purchaseOrder->supplier ? $payment->purchaseOrder->supplier->name : '' }}
                                                                </td>
                                                                <td>
                                                                    @if ($payment->purchaseOrder->balance_due == 'PO')
                                                                        OUT (PO)
                                                                    @else
                                                                        OUT (Sale Return Invoice)
                                                                    @endif
                                                                </td>
                                                                <td>{{ $payment->purchaseOrder->quote_no }}</td>
                                                                <td>{{ $payment->purchaseOrder->remark }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($payment->purchaseOrder->po_date)->format('d/m/Y') }}
                                                                </td>
                                                                <td>{{ $payment->payment_amount }}</td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                                $total -= $payment->payment_amount;
                                                            @endphp
                                                        @endforeach
                                                        @foreach ($expenses as $expense)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $expense->transaction->account->account_name }}
                                                                </td>
                                                                <td>N/A</td>
                                                                <td>OUT(Expense)</td>
                                                                <td></td>
                                                                <td>{{ $expense->description }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}
                                                                </td>
                                                                <td>{{ $expense->amount_mmk }}</td>

                                                            </tr>
                                                            @php
                                                                $no++;
                                                                $total -= $expense->amount_mmk;
                                                            @endphp
                                                        @endforeach

                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="7" class="text-right">Total</td>
                                                            <td>{{ $total }}</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <hr>
                                                <h6>Payment Table</h6>
                                                <table id="example9" class="table table-bordered table-striped mt-4">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Transfer Account</th>
                                                            <th>Account Name</th>
                                                            <th>Status</th>
                                                            <th>Opening Balance</th>
                                                            <th>Debit Voucher</th>
                                                            <th>Receiver Name</th>
                                                            <th>Reference No</th>
                                                            <th>Payment Date</th>
                                                            <th>Description</th>
                                                            <th>Amount</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $total_amt = 0; @endphp
                                                        @foreach ($add_payments as $index => $pay)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td> {{ $pay->transferAccount ? $pay->transferAccount->account_name : 'N/A' }}

                                                                </td>
                                                                <td>
                                                                    {{-- @foreach ($accounts as $account)
                                                                        @if ($pay->account_id == $account->id)
                                                                            {{ $account->account_name }}
                                                                        @endif
                                                                    @endforeach --}}
                                                                    {{ $pay->account->account_name }}
                                                                </td>
                                                                <td>
                                                                    {{ $pay->payment_status }}
                                                                </td>
                                                                <td>
                                                                    {{ $pay->opening_balance ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ $pay->debit_note }}</td>
                                                                <td>{{ $pay->receiver_name ?? 'N/A' }}</td>
                                                                <td>{{ $pay->reference_no ?? 'N/A' }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($pay->payment_date)->format('d-m-Y') ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ $pay->note ?? 'N/A' }}</td>
                                                                <td>{{ $pay->amount }}</td>
                                                                <td>
                                                                    {{-- <a href="{{ url('payment_details', $pay->id) }}"
                                                                        class="btn btn-primary "><i
                                                                            class="fa-solid fa-eye"></i></a> --}}
                                                                    <a href="{{ url('transaction_payment_edit', $pay->id) }}"
                                                                        class="btn btn-success"><i
                                                                            class="fa-solid fa-pen-to-square"></i></a>
                                                                    <a href="{{ url('transaction_delete_payment', $pay->debit_note) }}"
                                                                        class="btn btn-danger"
                                                                        onclick="return confirm('Are you sure you want to delete this payment ?')"><i
                                                                            class="fa-solid fa-trash"></i></a>
                                                                </td>
                                                            </tr>
                                                            @php $total_amt += (float) $pay->amount; @endphp
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="10" class="text-right">Total</td>
                                                            <td>{{ $total_amt }}</td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="invoice" role="tabpanel"
                                    aria-labelledby="invoice-tab">

                                    {{-- <div class="my-5 container-fluid">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <form id="invoiceSearchForm" method="get">
                                                    <div class="row">
                                                        <div class="col-6 col-md-4 col-lg-3 form-group">
                                                            <label for="start_date">Date From:</label>
                                                            <input type="date" name="start_date" id="start_date"
                                                                class="form-control" required>
                                                        </div>

                                                        <div class="col-6 col-md-4 col-lg-3 form-group">
                                                            <label for="end_date">Date To:</label>
                                                            <input type="date" name="end_date" id="end_date"
                                                                class="form-control" required>
                                                        </div>

                                                        <div class="col-4 col-md-4 col-lg-2 form-group">
                                                            <label for="">&nbsp;</label>
                                                            <input type="button" id="invoicesearchButton"
                                                                class="btn btn-primary form-control" value="Search"
                                                                style="background-color: #218838">
                                                        </div>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="card">

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="example2" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Invoice No.</th>
                                                            <th>Location</th>
                                                            <th>Deposit</th>
                                                            <th>Balance</th>
                                                            <th>Total</th>
                                                            <th>Payment Status</th>
                                                            <th>Invoice Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $no = '1';
                                                        @endphp
                                                        @foreach ($invoices as $invoice)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $invoice->invoice_no }}</td>
                                                                <td>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if ($warehouse->id == $invoice->branch)
                                                                            {{ $warehouse->name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>
                                                                <td>{{ $invoice->deposit }}</td>
                                                                <td>{{ $invoice->remain_balance }}</td>

                                                                <td>{{ $invoice->total }}</td>

                                                                @if ($invoice->total == $invoice->deposit)
                                                                    <td>Paid</td>
                                                                @elseif($invoice->total > $invoice->deposit && $invoice->deposit > 0)
                                                                    <td>Partial Paid</td>
                                                                @else
                                                                    <td>Unpaid</td>
                                                                @endif
                                                                <td>
                                                                    {{ $invoice->invoice_date }}
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach
                                                        @foreach ($receive_invoices as $invoice)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $invoice->invoice_no }}</td>
                                                                <td>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if ($warehouse->id == $invoice->branch)
                                                                            {{ $warehouse->name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>
                                                                <td>{{ $invoice->deposit }}</td>
                                                                <td>{{ $invoice->remain_balance }}</td>

                                                                <td>{{ $invoice->total }}</td>

                                                                @if ($invoice->total == $invoice->deposit)
                                                                    <td>Paid</td>
                                                                @elseif($invoice->total > $invoice->deposit && $invoice->deposit > 0)
                                                                    <td>Partial Paid</td>
                                                                @else
                                                                    <td>Unpaid</td>
                                                                @endif
                                                                <td>
                                                                    {{ $invoice->invoice_date }}
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach
                                                        @foreach ($sale_invoices as $sale_invoice)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $sale_invoice->invoice_no }}</td>
                                                                <td>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if ($warehouse->id == $sale_invoice->branch)
                                                                            {{ $warehouse->name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>
                                                                <td>{{ $sale_invoice->deposit }}</td>
                                                                <td>{{ $sale_invoice->remain_balance }}
                                                                </td>

                                                                <td>{{ $sale_invoice->total }}</td>

                                                                @if ($sale_invoice->total == $sale_invoice->deposit)
                                                                    <td>Paid</td>
                                                                @elseif($sale_invoice->total > $sale_invoice->deposit && $sale_invoice->deposit > 0)
                                                                    <td>Partial Paid</td>
                                                                @else
                                                                    <td>Unpaid</td>
                                                                @endif
                                                                <td>
                                                                    {{ $sale_invoice->invoice_date }}
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>


                                <div class="tab-pane fade" id="po" role="tabpanel" aria-labelledby="po-tab">

                                    {{-- <div class="my-5 container-fluid">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <form id="poSearchForm" method="get">
                                                    <div class="row">
                                                        <div class="col-md-3 form-group">
                                                            <label for="start_date">Date From:</label>
                                                            <input type="date" name="start_date"
                                                                class="form-control" id="po_start_date" required>
                                                        </div>
                                                        <div class="col-md-3 form-group">
                                                            <label for="end_date">Date To:</label>
                                                            <input type="date" name="end_date" id="po_end_date"
                                                                class="form-control" required>
                                                        </div>
                                                        <!-- Add your existing branch selection code here -->

                                                        <div class="col-md-2 form-group">
                                                            <label for="">&nbsp;</label>
                                                            <input type="submit" class="btn btn-primary form-control"
                                                                value="Search" id="poSearchButton"
                                                                style="background-color: #218838">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="card">

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="example4" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>PO No.</th>
                                                            <th>Location</th>
                                                            <th>Deposit</th>
                                                            <th>Balance</th>
                                                            <th>Total</th>
                                                            <th>PO Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $no = '1';
                                                        @endphp
                                                        @foreach ($purchase_orders as $po)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $po->quote_no }}</td>
                                                                <td>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if ($warehouse->id == $po->branch)
                                                                            {{ $warehouse->name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>

                                                                <td>{{ $po->deposit }}</td>
                                                                <td>{{ $po->remain_balance }}</td>
                                                                <td>{{ $po->total }}</td>

                                                                <td>
                                                                    {{ $po->po_date }}
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="pr" role="tabpanel" aria-labelledby="pr-tab">

                                    {{-- <div class="my-5 container-fluid">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <form id="prSearchForm" method="get">
                                                    <div class="row">
                                                        <div class="col-md-3 form-group">
                                                            <label for="start_date">Date From:</label>
                                                            <input type="date" name="start_date"
                                                                class="form-control" id="pr_start_date" required>
                                                        </div>
                                                        <div class="col-md-3 form-group">
                                                            <label for="end_date">Date To:</label>
                                                            <input type="date" name="end_date" id="pr_end_date"
                                                                class="form-control" required>
                                                        </div>
                                                        <!-- Add your existing branch selection code here -->

                                                        <div class="col-md-2 form-group">
                                                            <label for="">&nbsp;</label>
                                                            <input type="submit" class="btn btn-primary form-control"
                                                                value="Search" id="prSearchButton"
                                                                style="background-color: #218838">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="card">

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="example5" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>PO Return No.</th>
                                                            <th>Location</th>
                                                            <th>Deposit</th>
                                                            <th>Balance</th>
                                                            <th>Total</th>
                                                            <th>Invoice Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $no = '1';
                                                        @endphp
                                                        @foreach ($po_returns as $po)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $po->invoice_no }}</td>
                                                                <td>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if ($warehouse->id == $po->branch)
                                                                            {{ $warehouse->name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>

                                                                <td>{{ $po->deposit }}</td>
                                                                <td>{{ $po->remain_balance }}</td>
                                                                <td>{{ $po->total }}</td>

                                                                <td>
                                                                    {{ $po->invoice_date }}
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach
                                                        @foreach ($po_returns_receive as $po)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $po->invoice_no }}</td>
                                                                <td>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if ($warehouse->id == $po->branch)
                                                                            {{ $warehouse->name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>

                                                                <td>{{ $po->deposit }}</td>
                                                                <td>{{ $po->remain_balance }}</td>
                                                                <td>{{ $po->total }}</td>

                                                                <td>
                                                                    {{ $po->invoice_date }}
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="sr-inv" role="tabpanel"
                                    aria-labelledby="sr-inv-tab">
                                    {{--
                                    <div class="my-5 container-fluid">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <form id="saleReturnInvoiceForm" method="get">
                                                    <div class="row">
                                                        <div class="col-md-3 form-group">
                                                            <label for="start_date">Date From:</label>
                                                            <input type="date" name="start_date"
                                                                id="sr_inv_start_date" class="form-control" required>
                                                        </div>
                                                        <div class="col-md-3 form-group">
                                                            <label for="end_date">Date To:</label>
                                                            <input type="date" name="end_date"
                                                                id="sr_inv_end_date" class="form-control" required>
                                                        </div>
                                                        <!-- Add your existing branch selection code here -->

                                                        <div class="col-md-2 form-group">
                                                            <label for="">&nbsp;</label>
                                                            <input type="submit" class="btn btn-primary form-control"
                                                                value="Search" id="saleReturnInvoiceSearchButton"
                                                                style="background-color: #218838">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="card">

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="example6" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Sale Return No.</th>
                                                            <th>Location</th>
                                                            <th>Deposit</th>
                                                            <th>Balance</th>
                                                            <th>Total</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $no = '1';
                                                        @endphp
                                                        @foreach ($sale_return_invoices as $sr_inv)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $sr_inv->quote_no }}</td>
                                                                <td>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if ($warehouse->id == $sr_inv->branch)
                                                                            {{ $warehouse->name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>

                                                                <td>{{ $sr_inv->deposit }}</td>
                                                                <td>{{ $sr_inv->remain_balance }}</td>
                                                                <td>{{ $sr_inv->total }}</td>

                                                                <td>
                                                                    {{ $sr_inv->po_date }}
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="expense" role="tabpanel"
                                    aria-labelledby="expense-tab">

                                    {{-- <div class="my-5 container-fluid">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <form id="ExpenseSearchForm" method="get">
                                                    <div class="row">
                                                        <div class="col-md-3 form-group">
                                                            <label for="start_date">Date From:</label>
                                                            <input type="date" name="start_date"
                                                                id="expense_start_date" class="form-control" required>
                                                        </div>
                                                        <div class="col-md-3 form-group">
                                                            <label for="end_date">Date To:</label>
                                                            <input type="date" name="end_date"
                                                                id="expense_end_date" class="form-control" required>
                                                        </div>
                                                        <!-- Add your existing branch selection code here -->

                                                        <div class="col-md-2 form-group">
                                                            <label for="">&nbsp;</label>
                                                            <input type="submit" class="btn btn-primary form-control"
                                                                value="Search" id="ExpenseSearchButton"
                                                                style="background-color: #218838">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="card">

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="example7" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>

                                                            <th>No.</th>
                                                            <th>Name</th>
                                                            <th>Category</th>
                                                            <th>Branch</th>
                                                            <th>Description</th>
                                                            <th>Date</th>
                                                            <th>Amount(Kyat)</th>
                                                            {{-- <th>Amount(USD)</th> --}}



                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $no = '1';
                                                        @endphp
                                                        @foreach ($expenses as $expense)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $expense->name }}</td>
                                                                <td>
                                                                    {{ $expense->catego->name }}
                                                                </td>
                                                                <td>
                                                                    {{ $expense->warehouse->name }}
                                                                </td>
                                                                <td>{{ $expense->description }}</td>
                                                                <td>{{ $expense->date }}
                                                                </td>
                                                                <td>{{ $expense->amount_mmk }}</td>
                                                                {{-- <td>{{ ($expense->amount_usd, 2) }}</td> --}}
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="customer_return" role="tabpanel"
                                    aria-labelledby="customer_return-tab">

                                    {{-- <div class="my-5 container-fluid">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <form id="invoiceSearchForm" method="get">
                                                <div class="row">
                                                    <div class="col-6 col-md-4 col-lg-3 form-group">
                                                        <label for="start_date">Date From:</label>
                                                        <input type="date" name="start_date" id="start_date"
                                                            class="form-control" required>
                                                    </div>

                                                    <div class="col-6 col-md-4 col-lg-3 form-group">
                                                        <label for="end_date">Date To:</label>
                                                        <input type="date" name="end_date" id="end_date"
                                                            class="form-control" required>
                                                    </div>

                                                    <div class="col-4 col-md-4 col-lg-2 form-group">
                                                        <label for="">&nbsp;</label>
                                                        <input type="button" id="invoicesearchButton"
                                                            class="btn btn-primary form-control" value="Search"
                                                            style="background-color: #218838">
                                                    </div>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div> --}}

                                    <div class="card">

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="example2" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Invoice No.</th>
                                                            <th>Location</th>
                                                            <th>Deposit</th>
                                                            <th>Balance</th>
                                                            <th>Total</th>
                                                            <th>Payment Status</th>
                                                            <th>Invoice Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $no = '1';
                                                        @endphp
                                                        @foreach ($customer_return_invoices as $invoice)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $invoice->invoice_no }}</td>
                                                                <td>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if ($warehouse->id == $invoice->branch)
                                                                            {{ $warehouse->name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>
                                                                <td>{{ $invoice->deposit }}</td>
                                                                <td>{{ $invoice->remain_balance }}</td>

                                                                <td>{{ $invoice->total }}</td>

                                                                @if ($invoice->total == $invoice->deposit)
                                                                    <td>Paid</td>
                                                                @elseif($invoice->total > $invoice->deposit && $invoice->deposit > 0)
                                                                    <td>Partial Paid</td>
                                                                @else
                                                                    <td>Unpaid</td>
                                                                @endif
                                                                <td>
                                                                    {{ $invoice->invoice_date }}
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach
                                                        @foreach ($receive_customer_return as $invoice)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $invoice->invoice_no }}</td>
                                                                <td>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if ($warehouse->id == $invoice->branch)
                                                                            {{ $warehouse->name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>
                                                                <td>{{ $invoice->deposit }}</td>
                                                                <td>{{ $invoice->remain_balance }}</td>

                                                                <td>{{ $invoice->total }}</td>

                                                                @if ($invoice->total == $invoice->deposit)
                                                                    <td>Paid</td>
                                                                @elseif($invoice->total > $invoice->deposit && $invoice->deposit > 0)
                                                                    <td>Partial Paid</td>
                                                                @else
                                                                    <td>Unpaid</td>
                                                                @endif
                                                                <td>
                                                                    {{ $invoice->invoice_date }}
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach
                                                        @foreach ($sale_customer_return as $sale_invoice)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td>{{ $sale_invoice->invoice_no }}</td>
                                                                <td>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if ($warehouse->id == $sale_invoice->branch)
                                                                            {{ $warehouse->name }}
                                                                        @endif
                                                                    @endforeach
                                                                </td>
                                                                <td>{{ $sale_invoice->deposit }}</td>
                                                                <td>{{ $sale_invoice->remain_balance }}
                                                                </td>

                                                                <td>{{ $sale_invoice->total }}</td>

                                                                @if ($sale_invoice->total == $sale_invoice->deposit)
                                                                    <td>Paid</td>
                                                                @elseif($sale_invoice->total > $sale_invoice->deposit && $sale_invoice->deposit > 0)
                                                                    <td>Partial Paid</td>
                                                                @else
                                                                    <td>Unpaid</td>
                                                                @endif
                                                                <td>
                                                                    {{ $sale_invoice->invoice_date }}
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>

                            </div>

                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>

            </section>



        </div>
    </div>

    </section>

    </div>



    </div>

    <script src="{{ asset('plugins/jquery/jquery.min.js ') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            function getBranchInvoiceNo() {
                let warehouse_id = $('#location').val();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('get_branch_credit_no') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        warehouse_id: warehouse_id,
                    },
                    success: function(data) {
                        if (data.no) {
                            $('#voucher_no').val(data.no);
                        } else if (data.error) {
                            alert(data.error);
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseJSON.error || 'An unexpected error occurred.');
                    }
                });

            }

            getBranchInvoiceNo();
            setInterval(getBranchInvoiceNo, 3000);

        });

        $(document).ready(function() {
            $('#invoicesearchButton').on('click', function() {
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();
                let transactionId = {{ $transaction->id }};
                $.ajax({
                    url: "{{ route('account_invoice_search', ['id' => $transaction->id]) }}",
                    type: 'GET',
                    data: {
                        start_date: $('input[name="start_date"]').val(),
                        end_date: $('input[name="end_date"]').val(),
                    },
                    success: function(response) {
                        console.log(response);
                        let tbody = '';
                        let no = 1;
                        response.invoices.forEach(function(invoice) {
                            console.log(no);
                            console.log(invoice.total);
                            console.log(invoice.deposit);

                            function getPaymentStatus(invoice) {
                                if (invoice.total === invoice.deposit) {
                                    return 'Paid';
                                }
                                if (invoice.deposit >
                                    0 && invoice.total != invoice.deposit) {
                                    return 'Partial Paid';
                                }
                                return 'Unpaid';
                            }

                            let paymentStatus = getPaymentStatus(invoice);

                            // let paymentStatus = (invoice.total == invoice.deposit) ?
                            //     'Paid' :
                            //     (invoice.total > invoice.deposit && invoice.deposit >
                            //         0) ? 'Partial Paid' :
                            //     'Unpaid';

                            let warehouseName = '';
                            response.warehouses.forEach(function(warehouse) {
                                if (warehouse.id == invoice.branch) {
                                    warehouseName = warehouse.name;
                                }
                            });

                            tbody += `<tr>
                                        <td>${no}</td>
                                        <td>${invoice.invoice_no}</td>
                                        <td>${warehouseName}</td>

                                        <td>${Number(invoice.deposit).toLocaleString()}</td><td>${Number(invoice.remain_balance).toLocaleString()}</td>
                                        <td>${Number(invoice.total).toLocaleString()}</td>
                                        <td>${paymentStatus}</td>
                                        <td>${invoice.invoice_date}</td>
                                    </tr>`;
                            no++;
                        });
                        $('#example2 tbody').html(tbody);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#posSearchButton').on('click', function(e) {
                e.preventDefault();
                posformSearch();
            });

            function posformSearch() {
                let startDate = $('#pos_start_date').val();
                let endDate = $('#pos_end_date').val();
                let transactionId = {{ $transaction->id }};

                $.ajax({
                    url: "{{ url('account_pos_search', $transaction->id) }}",
                    type: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        let tbody = '';
                        let no = 1;

                        response.point_of_sales.forEach(function(pos) {
                            let warehouseName = '';
                            response.warehouses.forEach(function(warehouse) {
                                if (warehouse.id == pos.branch) {
                                    warehouseName = warehouse.name;
                                }
                            });

                            tbody += `<tr>
                                <td>${no}</td>
                                <td>${pos.invoice_no}</td>
                                <td>${warehouseName}</td>
                                <td>${pos.invoice_date}</td>
                                <td>${Number(pos.deposit).toLocaleString()}</td>
                                <td>${Number(pos.remain_balance).toLocaleString()}</td>
                                <td>${Number(pos.total).toLocaleString()}</td>
                                <td>${pos.sale_by}</td>
                            </tr>`;
                            no++;
                        });

                        $('#example3 tbody').html(tbody);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });


        $(document).ready(function() {
            $('#poSearchButton').on('click', function(e) {
                e.preventDefault();

                let startDate = $('#po_start_date').val();
                let endDate = $('#po_end_date').val();
                let transactionId = {{ $transaction->id }};

                $.ajax({
                    url: "{{ url('account_po_search', $transaction->id) }}",
                    type: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        let tbody = '';
                        let no = 1;

                        response.purchase_orders.forEach(function(po) {
                            let warehouseName = '';
                            response.warehouses.forEach(function(warehouse) {
                                if (warehouse.id == po.branch) {
                                    warehouseName = warehouse.name;
                                }
                            });

                            tbody += `<tr>
                                <td>${no}</td>
                                <td>${po.quote_no}</td>
                                <td>${warehouseName}</td>
                               ≈
                                <td>${Number(po.deposit).toLocaleString()}</td>
                                <td>${Number(po.remain_balance).toLocaleString()}</td>ß
                                <td>${Number(po.total).toLocaleString()}</td>
                                <td>${po.po_date}</td>
                              </tr>`;
                            no++;
                        });

                        $('#example4 tbody').html(tbody);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });


        $(document).ready(function() {
            $('#prSearchButton').on('click', function(e) {
                e.preventDefault();

                let startDate = $('#pr_start_date').val();
                let endDate = $('#pr_end_date').val();
                let transactionId = {{ $transaction->id }};

                $.ajax({
                    url: "{{ url('purchase_return_invoice_search', $transaction->id) }}",
                    type: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        let tbody = '';
                        let no = 1;

                        response.po_returns.forEach(function(po) {
                            let warehouseName = '';
                            response.warehouses.forEach(function(warehouse) {
                                if (warehouse.id == po.branch) {
                                    warehouseName = warehouse.name;
                                }
                            });



                            tbody += `<tr>
                        <td>${no}</td>
                        <td>${po.invoice_no}</td>
                        <td>${warehouseName}</td>
                        <td>${Number(po.deposit).toLocaleString()}</td>
                        <td>${Number(po.remain_balance).toLocaleString()}</td>
                        <td>${Number(po.total).toLocaleString()}</td>
                        <td>${po.invoice_date}</td>
                      </tr>`;
                            no++;
                        });

                        $('#example5 tbody').html(tbody);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });


        $(document).ready(function() {
            $('#saleReturnInvoiceSearchButton').on('click', function(e) {
                e.preventDefault();

                let startDate = $('#sr_inv_start_date').val();
                let endDate = $('#sr_inv_end_date').val();
                let transactionId = {{ $transaction->id }};

                $.ajax({
                    url: "{{ url('sale_return_invoice_search', $transaction->id) }}",
                    type: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        let tbody = '';
                        let no = 1;

                        response.sale_return_invoices.forEach(function(sr_inv) {
                            let warehouseName = '';
                            response.warehouses.forEach(function(warehouse) {
                                if (warehouse.id == sr_inv.branch) {
                                    warehouseName = warehouse.name;
                                }
                            });

                            tbody += `<tr>
                        <td>${no}</td>
                        <td>${sr_inv.quote_no}</td>
                        <td>${warehouseName}</td>
                        <td>${Number(sr_inv.deposit).toLocaleString()}</td>
                        <td>${Number(sr_inv.remain_balance).toLocaleString()}</td>
                        <td>${Number(sr_inv.total).toLocaleString()}</td>
                        <td>${sr_inv.po_date}</td>
                      </tr>`;
                            no++;
                        });

                        $('#example6 tbody').html(tbody);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
        $(document).ready(function() {
            $('#ExpenseSearchButton').on('click', function(e) {
                e.preventDefault();

                let startDate = $('#expense_start_date').val();
                let endDate = $('#expense_end_date').val();
                let transactionId = {{ $transaction->id }};

                $.ajax({
                    url: "{{ url('expense_search', $transaction->id) }}",
                    type: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        let tbody = '';
                        let no = 1;

                        response.expneses.forEach(function(expense) {
                            let warehouseName = '';
                            response.warehouses.forEach(function(warehouse) {
                                if (warehouse.id == expense.branch) {
                                    warehouseName = warehouse.name;
                                }
                            });

                            tbody += `<tr>
                        <td>${no}</td>
                        <td>${expense.name}</td>

                        <td>${expense.catego.name}</td>
                        <td>${warehouseName}</td>
                        <td>${expense.description??""}</td>
                        <td>${expense.date}</td>
                        <td>${expense.amount_mmk}</td>

                      </tr>`;
                            no++;
                        });

                        $('#example7 tbody').html(tbody);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });

        // $(document).ready(function() {
        //     $('#saleReturnPosSearchButton').on('click', function(e) {
        //         e.preventDefault();

        //         let startDate = $('#sr_pos_start_date').val();
        //         let endDate = $('#sr_pos_end_date').val();
        //         let transactionId = {{ $transaction->id }};

        //         $.ajax({
        //             url: "{{ url('sale_return_pos_search', $transaction->id) }}",
        //             type: 'GET',
        //             data: {
        //                 start_date: startDate,
        //                 end_date: endDate
        //             },
        //             success: function(response) {
        //                 let tbody = '';
        //                 let no = 1;

        //                 response.sale_return_pos.forEach(function(sr_pos) {
        //                     let warehouseName = '';
        //                     response.warehouses.forEach(function(warehouse) {
        //                         if (warehouse.id == sr_pos.branch) {
        //                             warehouseName = warehouse.name;
        //                         }
        //                     });

        //                     tbody += `<tr>
    //                 <td>${no}</td>
    //                 <td>${sr_pos.quote_no}</td>
    //                 <td>${warehouseName}</td>
    //                 <td>${Number(sr_pos.deposit).toLocaleString()}</td>
    //                 <td>${Number(sr_pos.remain_balance).toLocaleString()}</td>
    //                 <td>${Number(sr_pos.total).toLocaleString()}</td>
    //                 <td>${sr_pos.po_date}</td>
    //             </tr>`;
        //                     no++;
        //                 });

        //                 $('#example7 tbody').html(tbody);
        //             },
        //             error: function(xhr) {
        //                 console.error(xhr.responseText);
        //             }
        //         });
        //     });
        // });
    </script>

    <script>
        $(function() {
            $("#example1").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
            });
        });

        $(function() {
            $("#example2").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
            });
        });

        $(function() {
            $("#example3").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
            });
        });

        $(function() {
            $("#example4").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
            });
        });

        $(function() {
            $("#example5").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
            });
        });

        $(function() {
            $("#example6").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
            });
        });

        $(function() {
            $("#example7").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
            });
        });

        $(function() {
            $("#example9").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "filter": false,
                "pageLength": 30,
            });
        });
    </script>



    <script>
        $(document).ready(function() {
            $('#tableSelect').on('change', function() {
                const selectedValue = $(this).val();

                $('.tab-pane').removeClass('show active');

                $('#' + selectedValue).addClass('show active');
            });

            $('#tableSelect').trigger('change');
        });
    </script>
</body>

</html>
