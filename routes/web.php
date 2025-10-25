<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InOutController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandVariationsController;
use App\Http\Controllers\ProductListController;
use App\Http\Controllers\DeliveredItemsController;
use App\Http\Controllers\PurchaseOrderMakePaymentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/api-docs', function () {
        return view('api_docs');
    });
    // Route::post('/reset-deleted-at', function () {
    //     DB::table('items')->update(['deleted_at' => null]);
    //     DB::table('item_variations')->update(['deleted_at' => null]);
    //     DB::table('item_quantities')->update(['deleted_at' => null]);

    //     return back()->with('success', 'Deleted records restored!');
    // });
    // Route::get('/migrate', function () {
    //     Artisan::call('migrate');
    //     return 'Migrations have been run successfully.';
    // });

    //dashboard
    Route::get('home', [DashboardController::class, 'home'])->name('home');
    Route::get('dashboard/{branch?}', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('items', [ItemController::class, 'index']);
    Route::post('items_register', [ItemController::class, 'register']);
    //for item search to add
    Route::get('/item_search', [InvoiceController::class, 'item_search'])->name(
        'search_item_name_for_add'
    );
    Route::post('/item_search_fill', [InvoiceController::class, 'item_data_search_fill'])->name('item_data_search_fill');

    //Customer
    Route::get('customer', [CustomerController::class, 'index']);
    Route::get('customer_credit/{id}', [CustomerController::class, 'credit']);
    Route::get('all_customer_credit', [CustomerController::class, 'all_customer_credit']);
    Route::get('customer_invoice/{id}', [CustomerController::class, 'customer_invoice']);
    Route::post('customer_register', [CustomerController::class, 'store']);
    Route::get('customer_edit/{id}', [CustomerController::class, 'edit']);
    Route::post('customer_update/{id}', [CustomerController::class, 'update']);
    Route::get('customer_delete/{id}', [CustomerController::class, 'delete']);

    //Supplier
    Route::get('supplier', [SupplierController::class, 'index']);
    Route::post('supplier_register', [SupplierController::class, 'store']);
    Route::get('supplier_edit/{id}', [SupplierController::class, 'edit']);
    Route::post('supplier_update/{id}', [SupplierController::class, 'update']);
    Route::get('supplier_delete/{id}', [SupplierController::class, 'delete']);
    Route::get('all_supplier_credit', [SupplierController::class, 'all_supplier_credit']);
    Route::get('supplier_credit/{id}', [SupplierController::class, 'supplier_credit']);
    Route::get('supplier_po/{id}', [SupplierController::class, 'supplier_po']);


    //Location
    Route::get('warehouse', [WarehouseController::class, 'index'])->name('warehouse');
    Route::post('warehouse_register', [WarehouseController::class, 'warehouse_register']);
    Route::get('warehouse_Delete/{id}', [WarehouseController::class, 'warehouse_delete']);
    Route::get('warehouse_Edit/{id}', [WarehouseController::class, 'warehouse_edit']);
    Route::post('warehouse_Update/{id}', [WarehouseController::class, 'warehouse_Update']);
    Route::get('transfer_item', [WarehouseController::class, 'transfer_item']);
    Route::post('store_transfer_item', [WarehouseController::class, 'store_transfer_item'])->name('store_transfer_item');
    Route::post('/autocomplete-part-code-location', [WarehouseController::class, 'autocompletePartCode'])->name('autocomplete.part-code-location');
    Route::post('/get-part-data-location', [WarehouseController::class, 'getPartData'])->name('get.part.data-location');
    Route::get('/show_transfer_history', [WarehouseController::class, 'show_history']);
    Route::get('transfer_history_detail/{transfer_no}', [WarehouseController::class, 'transferHistoryDetail'])->name('transfer_history_detail');
    Route::post('get_branch_transfer_no', [WarehouseController::class, 'getBranchTransferNo'])->name('get_branch_transfer_no');
    Route::get('transfer_history_delete/{transfer_no}', [WarehouseController::class, 'transfer_history_delete'])->name('transfer_history_delete');
    Route::get('transfer_history_edit/{transfer_no}', [WarehouseController::class, 'transfer_history_edit'])->name('transfer_history_edit');
    Route::post('transfer_history_update/{transfer_no}', [WarehouseController::class, 'transfer_history_update'])->name('transfer_history_update');
    Route::get('/good_received/{transfer_no}', [WarehouseController::class, 'good_received']);

    //product list
    Route::get('product_list_manage', [ProductListController::class, 'index']);
    Route::get('product_list_register', [ProductListController::class, 'register']);
    Route::post('product_list_store', [ProductListController::class, 'store']);
    Route::get('product_list_edit/{product_list}', [ProductListController::class, 'edit']);
    Route::post('product_list_update/{product_list}', [ProductListController::class, 'update']);
    Route::get('product_list_delete/{product_list}', [ProductListController::class, 'delete']);
    Route::post('/pl_item_search', [ProductListController::class, 'item_search'])->name('product_list.search_item_name');
    Route::post('fill_item_data', [ProductListController::class, 'itemDataFill'])->name('product_list.item.fill');

    //Purchase Order
    Route::get('purchase_order_manage/{branch?}', [PurchaseOrderController::class, 'index'])->name('purchase_order');
    Route::get('purchase_order_register', [PurchaseOrderController::class, 'purchase_order_register'])->name('purchase_order_register');
    Route::post('purchase_order_store', [PurchaseOrderController::class, 'purchase_order_store'])->name('purchase_order_store');
    Route::get('purchase_order_delete/{id}', [PurchaseOrderController::class, 'po_delete'])->name('purchase_order_delete');
    Route::get('purchase_order_edit/{id}', [PurchaseOrderController::class, 'edit'])->name('purchase_order_edit');
    Route::post('purchase_order_update/{id}', [PurchaseOrderController::class, 'purchase_order_update'])->name('purchase_order_update');
    Route::get('purchase_order_details/{id}', [PurchaseOrderController::class, 'details'])->name('purchase_order_details');
    Route::post('/autocomplete-part-code-po', [PurchaseOrderController::class, 'autocompletePartCodePo'])->name('autocomplete-part-code-po');
    Route::post('/get-part-po-data', [PurchaseOrderController::class, 'getPartPoData'])->name('get-part-po-data');
    Route::post('/po-status-change', [PurchaseOrderController::class, 'poStatusChange'])->name('po-status-change');
    Route::post('/transit/{id}', [PurchaseOrderController::class, 'transit']);
    Route::get('/po_no_updates', [PurchaseOrderController::class, 'po_no_updates']);

    Route::get('/po_make_payment/{po}', [PurchaseOrderMakePaymentController::class, 'index'])->name('po-make-payment');
    Route::post('/po_make_payment_store/{po}', [PurchaseOrderMakePaymentController::class, 'paymentStore'])->name('po-make-payment-store');
    Route::get('po_voucher_view/{po_make_payment}', [PurchaseOrderMakePaymentController::class, 'poVoucherView'])->name('po_voucher_view');


    Route::post('/autocomplete_price', [PurchaseOrderController::class, 'autocomplete_price'])->name('autocomplete_price');
    Route::get('/supplier_service_search', [PurchaseOrderController::class, 'po_search'])->name('po_search');
    Route::post('/supplier_service_search_fill', [PurchaseOrderController::class, 'po_search_fill'])->name('po_search_fill');
    Route::post('/autocomplete-part-code', [PurchaseOrderController::class, 'autocompletePartCode'])->name('autocomplete.part-code');
    Route::post('/get-part-data', [PurchaseOrderController::class, 'getPartData'])->name('get.part.data');

    //Invoice
    Route::get('invoice/{branch?}', [InvoiceController::class, 'index'])->name('invoice');
    Route::post('invoice_register', [InvoiceController::class, 'invoice_register']);
    Route::get('invoice_reg', [InvoiceController::class, 'invoice']);
    Route::get('invoice_edit/{id}', [InvoiceController::class, 'invoice_edit']);
    Route::get('invoice_delete/{id}', [InvoiceController::class, 'invoice_delete']);
    Route::post('invoice_update/{id}', [InvoiceController::class, 'invoice_update']);
    Route::get('invoice_detail/{invoice}', [InvoiceController::class, 'invoice_detail'])->name('invoice_detail');
    Route::get('invoice_receipt_print/{invoice}', [InvoiceController::class, 'invoice_receipt_print'])->name('invoice_receipt_print');
    Route::get('daily_sales', [InvoiceController::class, 'daily_sales'])->name('daily_sales');
    Route::get('get_accounts_transaction', [InvoiceController::class, 'invoice_get_transaction'])->name('get_accounts_transaction');
    Route::get('get_accounts_transaction_quotation/{location}', [InvoiceController::class, 'quotation_get_transaction']);

    Route::get('get_accounts_transaction_po', [PurchaseOrderController::class, 'po_get_transaction'])->name('get_accounts_transaction_po');
    Route::get('/get_accounts_transaction_expense/{location}', [ExpenseController::class, 'expense_get_transaction']);
    Route::get('cash_voucher/{make_payment}', [InvoiceController::class, 'voucherView'])->name('voucher_view');
    Route::post('get_branch_invoice_no', [InvoiceController::class, 'getBranchInvoiceNo'])->name('get_branch_invoice_no');
    Route::get('invoice_no_updates', [InvoiceController::class, 'invoice_no_updates']);
    Route::post('get_branch_pos_no', [InvoiceController::class, 'getBranchPOSNo'])->name('get_branch_pos_no');
    Route::get('pos_no_updates', [InvoiceController::class, 'pos_no_updates']);

    Route::post('delivered_items_save', [DeliveredItemsController::class, 'save'])->name('delivered_items_save');
    Route::get('do_form/{no}', [DeliveredItemsController::class, 'doFormView'])->name('do_form');
    Route::get('daily_delivery', [DeliveredItemsController::class, 'daily_delivery']);
    Route::get('daily_delivery_search', [DeliveredItemsController::class, 'daily_delivery_search']);

    //customer return
    Route::get('customer_return', [InvoiceController::class, 'customer_return']);
    Route::get('customer_return_manage/{branch?}', [InvoiceController::class, 'customer_return_manage'])->name('customer_return');
    Route::get('customer_return_no_updates', [InvoiceController::class, 'customer_return_no_updates']);
    Route::post('get_branch_customer_return_no', [InvoiceController::class, 'getBranchCustomerReturnNo'])->name('get_branch_customer_return_no');


    //makepayment
    Route::get('make_payment/{id}', [InvoiceController::class, 'payment']);
    Route::get('cash_voucher_delete/{id}', [InvoiceController::class, 'cash_voucher_delete']);
    Route::post('make_payment_store/{id}', [InvoiceController::class, 'payment_store']);
    Route::get('payment_no_updates', [InvoiceController::class, 'payment_no_updates']);
    Route::get('cash_voucher_edit/{id}', [InvoiceController::class, 'payment_edit']);
    Route::post('payment_update/{id}', [InvoiceController::class, 'payment_update']);

    //deliver invoice
    Route::get('deliver/{invoice}', [InvoiceController::class, 'deliver'])->name('deliver');


    //Quotation
    Route::get('quotation/{branch?}', [InvoiceController::class, 'quotation'])->name('quotation');
    Route::get('/quotation_detail/{id}', [InvoiceController::class, 'quotation_detail']);
    Route::get('quotation_register', [InvoiceController::class, 'quotation_register']);
    Route::get('/customer_service_search', [InvoiceController::class, 'customer_service_search'])->name('customer_service_search');
    Route::post('/customer_service_search_fill', [InvoiceController::class, 'customer_service_search_fill'])->name('customer_service_search_fill');
    Route::get('quotation_delete/{id}', [InvoiceController::class, 'quotation_delete']);
    Route::get('quotation_edit/{id}', [InvoiceController::class, 'quotation_edit']);
    Route::get('change_invoice/{id}', [InvoiceController::class, 'change_invoice']);
    // Route::post('/autocomplete-part-code-invoice', [InvoiceController::class, 'autocompletePartCode'])->name('autocomplete.part-code-invoice');
    // Route::post('/autocomplete-barcode-invoice', [InvoiceController::class, 'autocompleteBarCode'])->name('autocomplete.barcode-invoice');

    //invoice , quotation , purchase order
    Route::post('/autocomplete-part-code', [InvoiceController::class, 'autocompletePartCodeInvoice'])->name('autocomplete-part-code-invoice');
    Route::post('/get-part-data', [InvoiceController::class, 'getPartDataInvoice'])->name('get-part-data-invoice');

    //item
    Route::get('items', [ItemController::class, 'index'])->name('item.index');
    Route::get('item_filter', [ItemController::class, 'item_filter'])->name('item.filter');

    Route::get('items_register', [ItemController::class, 'register']);
    Route::post('item_store', [ItemController::class, 'store']);
    Route::get('item_details/{id}', [ItemController::class, 'details']);
    Route::get('item_edit/{id}', [ItemController::class, 'edit']);
    Route::post('item_update/{id}', [ItemController::class, 'update']);
    Route::get('item_delete/{id}', [ItemController::class, 'delete']);
    Route::post('/delete-items', [ItemController::class, 'deleteItems'])->name('delete.items');
    Route::get('barcode/{item_id}/{id}', [ItemController::class, 'barcode']);
    Route::get('items_qty', [ItemController::class, 'itemsQty'])->name('item.itemqty');
    Route::get('new_order_items', [ItemController::class, 'newOrderItem'])->name('item.neworderitem');
    Route::get('item_delete_record', [ItemController::class, 'item_delete_record']);
    Route::get('restore_items/{id}', [ItemController::class, 'restore_items']);
    Route::post('select_restore_items', [ItemController::class, 'select_restore_items'])->name('restore_items');



    //inout
    Route::get('in_out/{id}', [ItemController::class, 'inout']);
    Route::get('stock_adjust/{id}', [InOutController::class, 'stock_adjust']);
    Route::get('damage_item/{id}', [InOutController::class, 'damage_item']);
    Route::post('in/{id}', [InOutController::class, 'in']);
    Route::post('out/{id}', [InOutController::class, 'out']);
    Route::get('/display_print/{items_id}/{id}', [InOutController::class, 'display_print'])->name('display_print');
    Route::get('invoice_record/{id}', [InOutController::class, 'invoice_record']);
    Route::get('purchase_record/{id}', [InOutController::class, 'purchase_order_reord']);
    Route::get('pos_record/{id}', [InOutController::class, 'pos_record']);
    Route::get('transfer_record/{id}', [InOutController::class, 'transfer_record']);

    //unit
    Route::get('unit', [UnitController::class, 'index']);
    Route::post('unit_store', [UnitController::class, 'unit_store']);
    Route::get('unit_edit/{id}', [UnitController::class, 'edit']);
    Route::post('unit_update/{id}', [UnitController::class, 'update']);
    Route::get('unit_delete/{id}', [UnitController::class, 'delete']);
    Route::get('get_part_data-unit', [UnitController::class, 'get_part_data_unit'])->name('get.part.data-unit');

    // //update exchange rate
    // Route::get('update_exchange', [ExchangeController::class, 'index']);
    // Route::post('save_exchange', [ExchangeController::class, 'save']);

    //Brand Category
    Route::get('brand_category', [CategoryController::class, 'index']);
    Route::post('brand_category_store', [CategoryController::class, 'store']);
    Route::get('brand_category_edit/{category}', [CategoryController::class, 'edit']);
    Route::post('brand_category_update/{category}', [CategoryController::class, 'update']);
    Route::get('brand_category_delete/{category}', [CategoryController::class, 'delete']);

    //Brand
    Route::get('brand', [BrandController::class, 'index']);
    Route::post('brand_store', [BrandController::class, 'store']);
    Route::get('brand_edit/{brand}', [BrandController::class, 'edit']);
    Route::post('brand_update/{brand}', [BrandController::class, 'update']);
    Route::get('brand_delete/{brand}', [BrandController::class, 'delete']);
    Route::post('/get_brand', [BrandController::class, 'getBrand'])->name('get.brand');

    //Brand Variation
    Route::get('brand_variations/{brand}', [BrandVariationsController::class, 'index'])->name('brand_variation');
    Route::post('brand_variation_store', [BrandVariationsController::class, 'store']);
    Route::get('brand_variation_edit/{variation}', [BrandVariationsController::class, 'edit']);
    Route::post('brand_variation_update/{variation}', [BrandVariationsController::class, 'update']);
    Route::get('brand_variation_delete/{variation}', [BrandVariationsController::class, 'delete']);
    Route::post('/get_model', [BrandVariationsController::class, 'getModel'])->name('get.brand.model');
    Route::post('/get_colour', [BrandVariationsController::class, 'getColour'])->name('get.brand.colour');
    Route::post('/get_size', [BrandVariationsController::class, 'getSize'])->name('get.brand.size');
    Route::post('/get_seater', [BrandVariationsController::class, 'getSeater'])->name('get.brand.seater');
    Route::post('/get_brand_variation', [BrandVariationsController::class, 'getBrandVariation'])->name('get.brand.variation.id');

    //expense
    Route::get('expense', [ExpenseController::class, 'index']);
    Route::post('expense_store', [ExpenseController::class, 'expenseStore']);
    Route::get('expense_edit/{expense}', [ExpenseController::class, 'edit']);
    Route::post('expense_update/{expense}', [ExpenseController::class, 'update']);
    Route::get('expense_delete/{expense}', [ExpenseController::class, 'delete']);
    Route::get('get_part_data-unit', [ExpenseController::class, 'get_part_data_unit'])->name('get.part.data-unit');
    Route::get('expense_category', [ExpenseCategoryController::class, 'index']);
    Route::post('expense_category_store', [ExpenseCategoryController::class, 'categoryStore']);
    Route::get('expense_category_edit/{id}', [ExpenseCategoryController::class, 'edit']);
    Route::post('expense_category_update/{id}', [ExpenseCategoryController::class, 'update']);
    Route::get('expense_category_delete/{id}', [ExpenseCategoryController::class, 'delete']);
    Route::get('expense_delete_record', [ExpenseController::class, 'expense_delete_record']);
    Route::get('restore_expense/{id}', [ExpenseController::class, 'restore_expense']);



    //POS
    Route::get('/pos_register', [InvoiceController::class, 'pos_register']);
    Route::get('pos_daily_sales', [InvoiceController::class, 'pos_daily_sales'])->name('pos_daily_sales');
    Route::get('pos', [InvoiceController::class, 'pos']);
    Route::post('/autocomplete-part-code-invoice', [InvoiceController::class, 'autocompletePartCode'])->name('autocomplete.part-code-invoice');
    Route::post('/get-part-data-invoice', [InvoiceController::class, 'getPartData'])->name('get.part.data-invoice');
    Route::post('/autocomplete-barcode-invoice', [InvoiceController::class, 'autocompleteBarCode'])->name('autocomplete.barcode-invoice');
    Route::post('/get-barcode-data-invoice', [InvoiceController::class, 'getBarcodeData'])->name('get.barcode.data-invoice');
    Route::get('pos_delete/{id}', [InvoiceController::class, 'pos_delete']);
    Route::post('/suspended', [InvoiceController::class, 'suspended'])->name('suspended');
    Route::get('/suspend_delete/{id}', [InvoiceController::class, 'suspend_delete']);

    //report
    Route::get('report_expense', [ReportController::class, 'reportExpense']);
    Route::get('report', [ReportController::class, 'report_invoice']);
    Route::get('/report_invoice/{branch?}', [ReportController::class, 'report_invoice'])->name('report_invoice');
    Route::get('report_quotation', [ReportController::class, 'report_quotation']);
    Route::get('/report_quotation_branch/{branch?}', [ReportController::class, 'report_quotation'])->name('report_quotation_branch');
    Route::get('report_po', [ReportController::class, 'report_po']);
    Route::get('report_po_branch/{branch?}', [ReportController::class, 'report_po'])->name('report_po_branch');
    Route::get('report_item', [ReportController::class, 'report_item']);
    Route::get('selling_report', [ReportController::class, 'selling_report']);
    Route::get('supplier_report', [ReportController::class, 'supplier_monthly_report']);


    Route::get('monthly_item_search', [ReportController::class, 'monthly_item_search']);
    Route::get('report_pos', [ReportController::class, 'report_pos']);
    Route::get('report_pos_branch', [ReportController::class, 'report_pos'])->name('report_pos_branch');
    Route::get('report_purchase_return', [ReportController::class, 'report_purchase_return']);
    Route::get('report_sale_return', [ReportController::class, 'report_sale_return']);
    Route::get('monthly_purchase_return', [ReportController::class, 'monthly_purchase_return']);
    Route::get('expense_search', [ReportController::class, 'expenseSearch']);
    Route::get('monthly_invoice_search', [ReportController::class, 'monthly_invoice_search']);
    Route::get('monthly_sale_return', [ReportController::class, 'monthly_sale_return']);
    Route::get('monthly_quotation_search', [ReportController::class, 'monthly_quotation_search']);
    Route::get('monthly_po_search', [ReportController::class, 'monthly_po_search']);
    Route::get('monthly_pos_search', [ReportController::class, 'monthly_pos_search']);
    Route::get('sale_return', [InvoiceController::class, 'sale_return']);
    Route::get('sale_return_register', [InvoiceController::class, 'sale_return_register']);
    Route::get('sale_return_edit/{id}', [InvoiceController::class, 'sale_return_edit']);
    Route::get('sale_return_detail/{id}', [InvoiceController::class, 'sale_return_detail']);
    Route::get('sale_return_delete/{id}', [InvoiceController::class, 'sale_return_delete']);
    Route::get('sale_return_search', [InvoiceController::class, 'sale_return_search']);
    Route::get('report_pos_receipt/{invoice}', [ReportController::class, 'pos_receipt']);
    Route::get('report_invoice_details/{invoice}', [ReportController::class, 'report_invoice_detail']);
    Route::get('general_ledger', [ReportController::class, 'general_ledger']);
    Route::get('/general_ledger/{branch?}', [ReportController::class, 'general_ledger'])->name('branch_general_ledger'); //
    Route::get('/general_ledger_detail/{id}', [ReportController::class, 'general_ledger_detail'])->name('general_ledger_detail'); //
    Route::get('general_ledger_search', [ReportController::class, 'general_ledger_search']);

    Route::get('profit_loss', [ReportController::class, 'profit_loss']);
    Route::get('profit_loss_search', [ReportController::class, 'profit_loss_search']);
    Route::get('profit_loss/{branch?}', [ReportController::class, 'profit_loss'])->name('branch_profit_loss');

    Route::get('balance_sheet', [ReportController::class, 'balance_sheet']);
    Route::get('balance_sheet/{branch?}', [ReportController::class, 'balance_sheet'])->name('branch_balance_sheet');

    Route::get('report_account_transaction_payment/{account_id}/{tran_id}', [ReportController::class, 'report_account_transaction_payment']);
    Route::get('report_account_transaction_payment_search/{id}', [ReportController::class, 'report_account_transaction_payment_search']);
    Route::get('receivable/{branch?}', [ReportController::class, 'receivable'])->name('receivable');
    Route::get('payable/{branch?}', [ReportController::class, 'payable'])->name('payable');
    Route::get('purchase_items', [ReportController::class, 'purchase_items']);
    Route::get('purchase_product_search', [ReportController::class, 'purchase_product_search']);
    Route::get('supplier_items/{id}/{start_date}/{end_date}', [ReportController::class, 'supplier_items']);

    //Profit
    Route::get('profit', [ProfitController::class, 'index']);
    Route::get('/index', [ProfitController::class, 'index'])->name('profit_report');
    Route::get('search', [ProfitController::class, 'search']);

    //Excel_Item_Export & Import
    Route::get('file-import-export', [ItemController::class, 'fileImportExport']);
    Route::post('file-import', [ItemController::class, 'fileImport'])->name('file-import');
    Route::get('file-export', [ItemController::class, 'fileExport'])->name('file-export');
    Route::post('file-update-import', [ItemController::class, 'fileUpdateImport'])->name('file-update-import');

    Route::get('file-import-template', [ItemController::class, 'fileImportTemplate'])->name('file-import-template');


    //customer_excel
    Route::get('customer_file_export', [CustomerController::class, 'fileExport'])->name('customer_file_export');
    Route::post('customer_file_import', [CustomerController::class, 'fileImport'])->name('customer_file_import');
    Route::get('customer_file_import_template', [CustomerController::class, 'fileImportTemplate'])->name('customer_file_import_template');

    //User Type
    Route::get('user_type', [UserTypeController::class, 'index']);
    Route::post('type_store', [UserTypeController::class, 'store']);
    Route::get('user_type_edit/{id}', [UserTypeController::class, 'edit']);
    Route::post('user_type_update/{id}', [UserTypeController::class, 'update']);
    Route::get('user_type_delete/{id}', [UserTypeController::class, 'delete']);


    //User
    Route::get('user', [UserController::class, 'user_register'])->name('user');
    Route::post('User_Register', [UserController::class, 'user_store']);
    Route::get('/delete_user/{id}', [UserController::class, 'delete_user']);
    Route::get('/delete_user/{id}', [UserController::class, 'delete_user']);
    Route::get('/userShow/{id}', [UserController::class, 'userShow']);
    Route::post('/update_user/{id}', [UserController::class, 'update_user']);
    Route::get('user_permission/{id}', [UserController::class, 'permission']);
    Route::post('user_permission_store/{id}', [UserController::class, 'permissionStore']);
    Route::post('/drop_table', [ItemController::class, 'drop_table'])->name('drop.table');


    //UserProfile
    Route::get('config_manage', [UserProfileController::class, 'index']);
    Route::get('config', [UserProfileController::class, 'config'])->name('config');
    Route::post('config_store', [UserProfileController::class, 'config_store'])->name('config_store');
    Route::get('config_edit/{id}', [UserProfileController::class, 'edit']);
    Route::get('config_delete/{id}', [UserProfileController::class, 'delete']);
    Route::post('config_update/{id}', [UserProfileController::class, 'config_edit']);
    Route::get('config_details/{id}', [UserProfileController::class, 'details']);

    //Account
    Route::get('/accountManagement', [AccountController::class, 'accountManagement'])->name('finance#accountManagement');
    Route::get('/accountManagement/{branch?}', [AccountController::class, 'accountManagement'])->name('branch_account');
    Route::post('account_register', [AccountController::class, 'account_register']);
    Route::get('account_edit/{id}', [AccountController::class, 'account_edit'])->name('account_edit');
    Route::post('account_update/{id}', [AccountController::class, 'account_update']);
    Route::get('account_delete/{id}', [AccountController::class, 'account_delete']);

    //Transaction
    Route::get('/transactionManagement', [TransactionController::class, 'transactionManagement'])->name('finance#transactionManagement');

    Route::get('/transactionManagement/{branch?}', [TransactionController::class, 'transactionManagement'])->name('branch_transaction');
    Route::post('transaction_register', [
        TransactionController::class,
        'transaction_register'
    ]);
    Route::get('/transactionManagementEdit/{id}', [TransactionController::class, 'transactionManagementEdit'])->name('finance#transactionManagementEdit');
    Route::post('/transaction_update/{id}', [TransactionController::class, 'transactionUpdate']);
    Route::get('/transaction_delete/{id}', [TransactionController::class, 'transactionDelete']);
    Route::get('/get-accounts', [TransactionController::class, 'getAccountsByLocation'])->name('get_accounts');

    //Payment
    Route::get('/payment/{id}', [PaymentController::class, 'payment']);
    Route::get('/report_payment/{id}', [PaymentController::class, 'payment']);
    Route::post('/transaction_payment_register/{id}', [PaymentController::class, 'payment_register']);
    Route::get('/transaction_delete_payment/{debit_note}', [PaymentController::class, 'paymentDelete']);
    Route::get('/transaction_payment_edit/{id}', [PaymentController::class, 'makePaymentEdit']);
    Route::put('/transaction_payment_update/{id}', [PaymentController::class, 'paymentUpdate']);
    Route::get('account_invoice_search/{id}', [PaymentController::class, 'account_invoice_search'])->name('account_invoice_search');
    Route::get('account_po_search/{id}', [PaymentController::class, 'account_po_search']);
    Route::get('account_pos_search/{id}', [PaymentController::class, 'account_pos_search'])->name('account_pos_search');
    Route::get('purchase_return_invoice_search/{id}', [PaymentController::class, 'purchase_return_invoice_search']);
    Route::get('sale_return_invoice_search/{id}', [PaymentController::class, 'sale_return_invoice_search']);
    Route::get('sale_return_pos_search/{id}', [PaymentController::class, 'sale_return_pos_search']);
    Route::get('expense_search/{id}', [PaymentController::class, 'expense_search']);
    Route::post('get_branch_credit_no', [PaymentController::class, 'getBranchTransactionNo'])->name('get_branch_credit_no');
    Route::get('payment_details/{id}', [PaymentController::class, 'details']);
    Route::get('/delete_record_payment/{id}', [PaymentController::class, 'delete_record_payment']);
    Route::get('/payments_restore/{id}', [PaymentController::class, 'restore_payment']);


    //setting
    Route::get('setting', [SettingController::class, 'index'])->name('setting');
    Route::get('setting/{branch?}', [SettingController::class, 'index'])->name('branch_setting');
    Route::post('setting_store', [SettingController::class, 'store']);
    Route::get('setting_edit/{id}', [SettingController::class, 'edit']);
    Route::post('setting_update/{id}', [SettingController::class, 'update']);
    Route::get('setting_delete/{id}', [SettingController::class, 'delete']);
    Route::get('invoice_setting', [SettingController::class, 'invoice']);
    Route::post('invoice_setting', [SettingController::class, 'invoice'])->name('invoice_setting');
    Route::post('invoice_setting/edit', [SettingController::class, 'invoice_setting_edit'])->name('invoice_setting_edit');
    Route::post('invoice_setting_delete', [SettingController::class, 'invoice_setting_delete'])->name('invoice_setting_delete');
    Route::post('receivable_invoice_setting', [SettingController::class, 'receivable_invoice'])->name('receivable_invoice_setting');
    Route::post('receivable_invoice_setting/edit', [SettingController::class, 'receivable_invoice_setting_edit'])->name('receivable_invoice_setting_edit');
    Route::post('receivable_invoice_setting_delete', [SettingController::class, 'receivable_invoice_setting_delete'])->name('receivable_invoice_setting_delete');
    Route::post('payable_po_setting', [SettingController::class, 'payable_po'])->name('payable_po_setting');
    Route::post('payable_po_setting/edit', [SettingController::class, 'payable_po_setting_edit'])->name('payable_po_setting_edit');
    Route::post('payable_po_setting_delete', [SettingController::class, 'payable_po_setting_delete'])->name('payable_po_setting_delete');
    Route::post('expense_setting', [SettingController::class, 'expense'])->name('expense_setting');
    Route::post('expense_setting/edit', [SettingController::class, 'expense_setting_edit'])->name('expense_setting_edit');
    Route::post('expense_setting_delete', [SettingController::class, 'expense_setting_delete'])->name('expense_setting_delete');


    Route::post('purchase_order_setting', [SettingController::class, 'purchase_order'])->name('purchase_order_setting');
    Route::post('purchase_order_setting/edit', [SettingController::class, 'purchase_order_setting_edit'])->name('purchase_order_setting_edit');
    Route::post('purchase_order_setting_delete', [SettingController::class, 'purchase_order_setting_delete'])->name('purchase_order_setting_delete');

    Route::post('pos_setting', [SettingController::class, 'pos'])->name('pos_setting');
    Route::post('pos_setting/edit', [SettingController::class, 'pos_setting_edit'])->name('pos_setting_edit');
    Route::post('pos_setting_delete', [SettingController::class, 'pos_setting_delete'])->name('pos_setting_delete');


    Route::post('purchase_return_setting', [SettingController::class, 'purchase_return'])->name('purchase_return_setting');
    Route::post('purchase_return_setting/edit', [SettingController::class, 'purchase_return_setting_edit'])->name('purchase_return_setting_edit');
    Route::post('purchase_return_setting_delete', [SettingController::class, 'purchase_return_setting_delete'])->name('purchase_return_setting_delete');

    Route::post('sale_return_invoice_setting', [SettingController::class, 'sale_return_invoice'])->name('sale_return_invoice_setting');
    Route::post('sale_return_invoice_setting/edit', [SettingController::class, 'sale_return_invoice_setting_edit'])->name('sale_return_invoice_setting_edit');
    Route::post('sale_return_invoice_setting_delete', [SettingController::class, 'sale_return_invoice_setting_delete'])->name('sale_return_invoice_setting_delete');


    Route::post('sale_return_pos_setting', [SettingController::class, 'sale_return_pos'])->name('sale_return_pos_setting');
    Route::post('sale_return_pos_setting/edit', [SettingController::class, 'sale_return_pos_setting_edit'])->name('sale_return_pos_setting_edit');
    Route::post('sale_return_pos_setting_delete', [SettingController::class, 'sale_return_pos_setting_delete'])->name('sale_return_pos_setting_delete');

    // Route::get('/migrate', function () {
    //     \Artisan::call('migrate');
    //     return 'Migration run successfully!';
    // });
});
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


// Test
// Route::get('/update_barcode', [ItemController::class, 'update_barcode'])->name('update_barcode');

require __DIR__ . '/auth.php';
