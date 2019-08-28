<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\ClientHistory;
use App\Expense;
use App\PaidUtility;
use App\Month;
use App\OrdersNumber;
use App\Place;
use App\Product;
use App\Subdivision;
use App\Supplier;
use App\Warehouse;
use Illuminate\Http\Request;
use App\Client;
use App\Good;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class User7Controller extends Controller
{
    protected $redirectTo = 'user7';

    /**
     * return home page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (\Request::isMethod('get')) {
            return view('user7.home');
        }
        return abort(404);
    }

    /**
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showReceivingOrdersPage()
    {

        if (\Request::isMethod('get') && \Request::ajax()) {
            return view('user7.receiving_order');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return User7Controller
     */
    public function getDataReceivingOrder(Request $request)
    {
        $orders = OrdersNumber::select('clients.id as client_id', 'clients.name as client_name',
            'products.id as product_id', 'products.name as product_name', 'products.height as product_height',
            'products.local_price as local_price', 'products.export_price as export_price', 'orders.amt as product_amt', 'orders.price as order_price',
            'orders.created_at as date')
            ->join('orders', 'orders.orders_number_id', '=', 'orders_number.id')
            ->join('products', 'products.id', '=', 'orders.product_id')
            ->join('clients', 'clients.id', '=', 'orders_number.client_id')
            ->whereNull('not_enough')
            ->where([
                'orders_number.exit_ware_status' => 1,
                'orders_number.confirmed' => 1,
            ])->when($request, function ($query) use ($request) {
                if ($request->sortBy) {
                    $query->orderBy($request->sortBy, $request->direction);
                }
                if ($request->from) {
                    $date_from = Carbon::parse($request->from);
                    $query->where('orders.created_at', '>', $date_from);
                }
                if ($request->to) {
                    $date_to = Carbon::parse($request->to . '24:00:00');
                    $query->where('orders.created_at', '<', $date_to);
                }
                if ($request->id) {
                    if ($request->group_by == 'product_id') {
                        $query->where('product_id', $request->id);
                    } else if ($request->group_by == 'client_id') {
                        $query->where('orders_number.client_id', $request->id);
                    }
                }
                if ($request->name) {
                    if ($request->group_by && $request->group_by == 'product_id') {
                        $query->where('products.name', 'ilike', $request->name."%");
                    } else if ($request->group_by && $request->group_by == 'client_id') {
                        $query->where('clients.name', 'ilike', $request->name."%");
                    }
                }
            })->get();

        if ($request->isMethod('post')) {
            if (!$request->has('id')) {
                $orders = $orders->groupBy($request->group_by)->toArray();
                $result = ClientHistory::select('goods.name as good_name', 'client_histories.client_id as client_id',
            'client_histories.good_amt as good_amt', 'client_histories.good_id as good_id', 'client_histories.debt as debt')
                    ->leftJoin('goods', 'goods.id', '=', 'client_histories.good_id')
                    ->when($request, function ($query) use ($request) {
                        if ($request->from) {
                            $date_from = Carbon::parse($request->from);
                            $query->where('client_histories.created_at', '>', $date_from);
                        }
                        if ($request->to) {
                            $date_to = Carbon::parse($request->to . '24:00:00');
                            $query->where('client_histories.created_at', '<', $date_to);
                        }
                    })->get();

                $collect = collect();

                foreach ($orders as $item) {

                    $total_price = 0;
                    $total_amt = 0;
                    $res = $result->where('client_id', $item[0]['client_id']);
                    foreach ($item as $x) {
                        $total_price += $x['order_price'];
                        $total_amt += $x['product_amt'];
                    }
                    $item[0]['total_amt'] = $total_amt;
                    $item[0]['total_price'] = $total_price;
                    $item[0]['debt'] = $res->sum('debt');
                    $good_item = 'bucket';
                    foreach ($res as $good){
                        $item[0][$good_item] = $good->sum('good_amt');
                        $good_item = 'lid';
                    }
                    $collect->push($item[0]);

                }
                return $collect;
            }
            return $orders;
        }

        if ($request->isMethod('get')) {
            if ($request->has('export')) {
                return $this->exportData($orders->toArray());
            }
        }

        return abort(404);
    }

    /**
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAcceptMoneyPage()
    {

        if (\Request::isMethod('get') && \Request::ajax()) {
            return view('user7.accept_money');
        }
        return abort(404);
    }

    /**
     *
     */
    public function acceptFromCustomer(){
        if (\Request::isMethod('get') && \Request::ajax()){
            $ware = Warehouse::select('warehouses.id as id', 'warehouses.amt as amt',
                'warehouses.created_at as date', 'warehouses.balance as balance', 'goods.name as good_name', 'warehouses.cost as cost',
                'goods.unit as good_unit', 'goods.id as good_id', 'warehouses.price as good_price',
                'clients.name as client_name', 'clients.id as client_id', 'warehouses.created_at as date')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->leftJoin('clients', 'clients.id', '=', 'warehouses.client_id')
                ->where(['paid' => 1])
                ->whereNotNull('client_id')
                ->get();
//            if (is_null($id)){
//                $ware = $ware->groupBy('supplier_id')->toArray();
//            }
            return $ware;
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|array|\Illuminate\Http\JsonResponse
     */
    public function AcceptMoneyPost(Request $request)
    {
        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(), [
                    'id*' => 'required',
                    'paid*' => 'required',
                ]
            );

            if ($validate->fails()) {
                return response()->json([
                    'errors' => $validate->errors(),
                    'status' => 'Failed']);
            }

            foreach ($request->data as $data) {
                $client_history = new ClientHistory;
                $client_history->client()->associate($data['id']);
                $client_history->debt = -$data['paid'];
                $client_history->save();

            }
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function getClients(Request $request)
    {
        if ($request->isMethod('post')) {
            $clients = Client::select('id', 'name', 'phone', 'address')
                ->when($request, function ($query) use ($request) {
                    if ($request->sortBy) {
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })->get();
            return $clients;
        }
        return abort(404);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function showAddExpensesPage()
    {
        if (\Request::isMethod('get') && \Request::ajax()) {
            $goods = Good::select('id', 'name', 'unit')
                ->with('supplier')
                ->orderBy('name', 'asc')
                ->get();
            $clients = Client::select('id', 'name')->get();
            $view = view('user7.add_expenses')->render();

            return [
                'goods' => $goods,
                'view' => $view,
                'clients' => $clients,
            ];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function acceptExpenses(Request $request){

        if ($request->isMethod('post')) {
            foreach ($request->data as $item) {
                $ware = new Warehouse;
                $ware->good_id = $item['good_id'];
                $ware->amt = $item['amt'];
                $ware->price = $item['price'];
                $ware->cost = is_null($item['price'])? null : $item['amt'] * $item['price'];

                if (isset($item['supplier'])) $ware->supplier_id = $item['supplier'];
                else if (isset($item['client'])) $ware->client_id = $item['client'];
                $ware->save();
            }

            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @param null $id
     */
    public function payGoodData($id = null){
        if (\Request::isMethod('get') && \Request::ajax()){
            $ware = Warehouse::select('warehouses.id as id', 'warehouses.amt as amt',
                'warehouses.created_at as date', 'warehouses.balance as balance', 'goods.name as good_name', 'warehouses.cost as cost',
                'goods.unit as good_unit', 'goods.id as good_id', 'warehouses.price as good_price',
                'suppliers.name as supplier_name', 'suppliers.id as supplier_id', 'warehouses.created_at as date')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'warehouses.supplier_id')
                ->where(['paid' => 1])
                ->whereNotNull('supplier_id')
                ->when($id, function ($query) use ($id){
                    if (!is_null($id)){
                        $query->where('warehouses.supplier_id', $id);
                    }
                    return $query;
                })
                ->get();
            if (is_null($id)){
                $ware = $ware->groupBy('supplier_id')->toArray();
            }
            return $ware;
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function accessPayGood(Request $request){
        if ($request->isMethod('post') && $request->ajax()){
            $ware = Warehouse::where([
                'paid' => '1',
                'supplier_id' => $request->id
            ])->update(['paid' => 3]);
            return $ware ? ['status' => 'success'] : ['status' => 'failed'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function returnedGoodData(Request $request){
        if ($request->isMethod('post') && $request->ajax()){
            $goods = Warehouse::select('warehouses.amt as amt', 'warehouses.id as ware_id', 'warehouses.amt as amt', 'clients.name as client_name',
                'suppliers.name as supplier_name', 'warehouses.client_id as client_id', 'warehouses.supplier_id as supplier_id',
                'goods.name as good_name', 'goods.id as good_id', 'goods.unit as good_unit' )
                ->leftJoin('clients', 'clients.id', '=', 'warehouses.client_id')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'warehouses.supplier_id')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->when($request, function ($query) use ($request){
                    if ($request->id){
                        $query->where('supplier_id', $request->id);
                    }
                    return $query;
                })
                ->where('paid', 2)
                ->whereNotNull('supplier_id')
                ->get();
            if (!$request->id){
                $goods = $goods->groupBy('supplier_id');
            }

            return $goods;
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function accessReturnedGood(Request $request){
        if ($request->isMethod('post') && $request->ajax()){
           $ware = Warehouse::where([
               'paid' => 2,
               'supplier_id' => $request->id,
           ])->update(['paid' => 0]);

            return $ware ? ['status' => 'success'] : ['status' => 'field'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     */
    public function updateReturnedGood(Request $request){
        if ($request->isMethod('post') && $request->ajax()){
            Warehouse::find($request->record['ware_id'])->update([
                'amt' => $request->record['amt']
            ]);
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function deleteReturnedGoodDetail(Request $request){
        if ($request->isMethod('delete') && $request->ajax()){
            $ware = Warehouse::find($request->id)->delete();
            return $ware ? ['status' => 'success'] : ['status' => 'failed'];
        }
        return abort(404);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function showAddUtilitiesPage(){
        if (\Request::isMethod('get') && \Request::ajax()) {
            $utilities = Expense::all();
            $months = Month::select('id', 'name')->get();
            return [
                'view' => view('user7.accept_utilities')->render(),
                'utilities' => $utilities,
                'months' => $months,
            ];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function acceptUtilities(Request $request){
        if ($request->isMethod('post')) {
            foreach ($request->records as $item) {
                $paid_utility = new PaidUtility;
                $paid_utility->expense_id = $item['id'];
                $paid_utility->month_id = $item['month'];
                $paid_utility->amt = $item['amt'];
                $paid_utility->balance = $item['price'];
                $paid_utility->save();
            }
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function showExpensesHistoryPage(Request $request){

        if ($request->isMethod('get')) {
            return view('user7.expenses_history');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function paidGoodsHistory(Request $request) {

        $ware = Warehouse::select('warehouses.id as id', 'warehouses.amt as amt',
            'warehouses.created_at as date_of_created', 'warehouses.updated_at as date_of_updated', 'warehouses.balance as balance', 'goods.name as good_name', 'warehouses.cost as cost',
            'goods.unit as good_unit', 'goods.id as good_id', 'warehouses.price as good_price',
            'suppliers.name as supplier_name', 'suppliers.id as supplier_id', 'warehouses.created_at as date')
            ->join('goods', 'goods.id', '=', 'warehouses.good_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'warehouses.supplier_id')
            ->where(['paid' => 3])
            ->whereNotNull('supplier_id')
            ->when($request, function ($query) use ($request) {
                if ($request->sortBy) {
                    $query->groupBy($request->sortBy, $request->direction);
                }
                if ($request->name) {
                    $query->where('suppliers.name', 'ilike', $request->name . "%");
                }
                if ($request->from) {
                    $date_from = Carbon::parse($request->from);
                    $query->where('warehouses.created_at', '>', $date_from);
                }
                if ($request->to) {
                    $date_to = Carbon::parse($request->to . '24:00:00');
                    $query->where('warehouses.created_at', '<', $date_to);
                }
                if ($request->id) {
                    $query->where('warehouses.supplier_id', $request->id);
                }
                return $query;

            })->orderBy('date', 'desc')
            ->get();

        if (!$request->id){
            $ware = $ware->groupBy('supplier_id')->toArray();
        }

        if ($request->isMethod('get')) {
            $this->exportExpenseHistory($ware->toArray());
        }


        if ($request->isMethod('post')) {
            return $ware;
        }
        return abort(404);
    }

    /**
     * @param Request $request
     */
    public function paidUtilitiesHistory(Request $request){

        $paid_utilities = PaidUtility::select('paid_utilities.id as id',
            'paid_utilities.amt as amt', 'paid_utilities.created_at as date', 'paid_utilities.month_id as month_id',
            'paid_utilities.balance as balance', 'expenses.name as expense_name',
            'expenses.id as expenses_id', 'months.name as month_name')
            ->join('expenses', 'expenses.id', '=', 'paid_utilities.expense_id')
            ->join('months', 'months.id', '=', 'paid_utilities.month_id')
            ->when($request, function ($query) use ($request) {
                if ($request->sortBy) {
                    $query->groupBy($request->sortBy, $request->direction);
                }
                if ($request->name) {
                    $query->where('expenses.name', 'ilike', $request->name . "%");
                }
                if ($request->from) {
                    $date_from = Carbon::parse($request->from);
                    $query->where('paid_utilities.created_at', '>', $date_from);
                }
                if ($request->to) {
                    $date_to = Carbon::parse($request->to . '24:00:00');
                    $query->where('paid_utilities.created_at', '<', $date_to);
                }
                if ($request->month) {
                    $query->where('months.id', $request->month);
                }
                return $query;

            })->get();

        if (!$request->month) {
            $paid_utilities = $paid_utilities->groupBy('month_id')->toArray();
        }

//        if ($request->isMethod('get')){
//            $this->exportExpenseHistory($paid_goods->toArray());
//        }


        if ($request->isMethod('post')) {
            return $paid_utilities;
        }
        return abort(404);
    }

    /**
     * return view for goods page
     * @return array
     * @throws \Throwable
     */
    public function showGoodPage()
    {

        if (\Request::isMethod('get') && \Request::ajax()) {
            $subdivisions = Subdivision::select('id', 'name')->get();
            $suppliers = Supplier::select('id', 'name')->get();

            return [
                'view' => view('user7.goods', [
                    'suppliers' => $suppliers,
                    'subdivisions' => $subdivisions,
                ])->render(),
                'suppliers' => $suppliers,
                'subdivisions' => $subdivisions,
            ];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function goodData(Request $request)
    {
        if ($request->isMethod('post')) {
            $goods = Good::select('goods.id as id', 'goods.returnable as returnable', 'goods.name as name', 'unit',
                'subdivisions.id as subdivision_id', 'subdivisions.name as subdivision_name')
                ->with('supplier')
                ->join('subdivisions', 'subdivisions.id', '=', 'subdivision_id')
                ->when($request, function ($query) use ($request) {
                    if ($request->sortBy) {
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('name', 'asc')
                ->paginate($request->limit);
            $goods = $goods->toArray();

            $data = [
                'records' => $goods['data'],
                'total' => $goods['total'],
            ];
            return $data;
        }
        return abort(404);
    }

    /**
     * add goods
     * @param Request $request
     * @return $this|array|\Illuminate\Http\JsonResponse
     */
    public function addGood(Request $request){

        if ($request->isMethod('post')) {

            $rules = [
                'name' => 'required',
                'unit' => 'required',
                'sub_id' => 'required',
                'supplier_id' => 'required',
            ];

            $validate = Validator::make($request->record, $rules);

            if ($validate->fails()) {
                return response()->json([
                    'errors' => $validate->errors(),
                    'status' => 'Failed']);
            }

            $good = new Good;
            $good->name = $request->record['name'];
            $good->unit = $request->record['unit'];
            $good->subdivision_id = $request->record['sub_id'];
            $good->save();
            $good->supplier()->attach($request->record['supplier_id']);

            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * update good
     * @param Request $request
     * @return array
     */
    public function updateGood(Request $request)
    {
        if ($request->isMethod('post')) {

            $good = Good::find($request->record['id']);
            $good->name = $request->record['name'];
            $good->unit = $request->record['unit'];
            $good->returnable = $request->record['returnable'] ==  'true' ? 1 : 0;
            $good->subdivision()->associate($request->record['subdivision_id']);
            $good->supplier()->sync(isset($request->record['supplier']) ? $request->record['supplier'] : []);
            $good->save();
            return ($good) ? ['status' => 'success'] : ['status' => 'failed'];
        }
        return abort(404);
    }

    /**
     * delete good
     * @param Request $request
     * @return $this|array
     * @throws \Exception
     */
    public function deleteGood(Request $request)
    {
        if ($request->isMethod('delete')) {
            Good::where('id', $request->id)->delete();
            return ['status' => 'Deleted'];
        }
        return abort(404);
    }

    /**
     * return view product page
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showProductPage()
    {
        if (\Request::isMethod('get') && \Request::ajax()) {
            return view('user7.products');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function productData(Request $request)
    {

        if ($request->isMethod('post')) {

            $product = Product::select('id', 'name', 'height', 'local_price', 'export_price')
                ->when($request, function ($query) use ($request) {
                    if ($request->sortBy) {
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('name', 'ASC')
                ->paginate($request->limit)
                ->toArray();
            $data = [
                'records' => $product['data'],
                'total' => $product['total'],
            ];
            return $data;
        }
        return abort(404);

    }

    /**
     * @param Request $request
     * @return array
     */
    public function addProductExcel(Request $request)
    {

        if ($request->hasFile('excel')) {
            $validate = Validator::make(['file' => $request->excel],
                ['file' => '|mimes:xlsx,xls',]
            );

            if ($validate->fails()) {
                return response()->json([
                    'errors' => $validate->errors(),
                    'status' => 'failed']);
            }
            Excel::selectSheetsByIndex(0)->load($request->excel->path(), function ($reader) {
                $file = $reader->toArray();
                $data = array_map(function ($item) {
                    return [
                        'name' => $item['անուն'],
                        'height' => $item['բոյ'],
                        'local_price' => $item['տեղ.գին'],
                        'export_price' => $item['արտ.գին']
                    ];
                }, $file);

                foreach ($data as $item) {

                    Product::create($item);
                }
            });
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * add products
     * @param Request $request
     * @return $this|array|\Illuminate\Http\JsonResponse
     */
    public function addProduct(Request $request)
    {

        if ($request->isMethod('post')) {
            $rules = [
                'name*' => 'required|unique_with:products,height*',
                'height*' => 'required|integer:products',
                'local_price*' => 'required|integer:products',
                'export_price*' => 'required|integer:products',
            ];

            $validate = Validator::make($request->record, $rules);

            if ($validate->fails()) {
                return response()->json([
                    'errors' => $validate->errors(),
                    'status' => 'Failed']);
            }

            foreach ($request->record as $data) {

                Product::create([
                    'name' => $data['name'],
                    'height' => $data['height'],
                    'local_price' => $data['local_price'],
                    'export_price' => $data['export_price']
                ]);
            }

            return ['status' => 'success'];
        }
        return abort(404);

    }

    /**
     * update product
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProduct(Request $request)
    {

        if ($request->isMethod('post')) {
            $rules = [
                'name' => 'required',
                'height' => 'required|integer:products',
                'local_price' => 'required|integer:products',
                'export_price' => 'required|integer:products',
            ];

            $validate = Validator::make($request->record, $rules);

            if ($validate->fails()) {
                return response()->json([
                    'errors' => $validate->errors(),
                    'status' => 'Failed']);
            }

            $product = Product::find($request->record['id'])->update([
                'name' => $request->record['name'],
                'height' => $request->record['height'],
                'local_price' => $request->record['local_price'],
                'export_price' => $request->record['export_price'],
            ]);
            if ($product) {
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'failed']);
        }
        return abort(404);

    }

    /**
     * delete product
     * @param Request $request
     * @return $this|array
     * @throws \Exception
     */
    public function deleteProduct(Request $request)
    {
        if ($request->isMethod('delete')) {
            Product::where('id', $request->id)->delete();
            return ['status' => 'Deleted'];
        }
        return abort(404);
    }

//places
// places
//    /**
//     * show internal destinations page
//     * @return array
//     * @throws \Throwable
//     */
//    public function showPlacePage()
//    {
//        if (\Request::isMethod('get') && \Request::ajax()) {
//            $subdivisions = Subdivision::select('id', 'name')->get();
//            return [
//                'view' => view('user7.places', ['subdivisions' => $subdivisions])->render(),
//                'subdivisions' => $subdivisions
//            ];
//        }
//        return abort(404);
//    }
//
//    /**
//     * @param Request $request
//     * @return array
//     */
//    public function placeData(Request $request) {
//
//        if ($request->isMethod('post')) {
//            $place = Place::select('id', 'name')
//                ->when($request, function ($query) use ($request) {
//                    if ($request->sortBy) {
//                        $query->orderBy($request->sortBy, $request->direction);
//                    }
//                    return $query;
//                })
//                ->with('subdivision')
//                ->orderBy('name')
//                ->paginate($request->limit)
//                ->toArray();
//            $data = [
//                'records' => $place['data'],
//                'total' => $place['total'],
//            ];
//            return $data;
//        }
//        return abort(404);
//    }
//
//    /**
//     * update place
//     * @param Request $request
//     * @return array
//     */
//    public function updatePlace(Request $request){
//        if ($request->isMethod('post')) {
//
//            $sub = Subdivision::find($request->record['subdivision'])->first();
//            $place = Place::find($request->record['id']);
//            $place->name = $request->record['name'];;
//            $place->save($sub);
//            return ($place) ? ['status' => 'success'] : ['status' => 'failed'];
//        }
//        return abort(404);
//    }
//
//    /**
//     * delete place
//     * @param Request $request
//     * @return array
//     * @throws \Exception
//     */
//    public function deletePlace(Request $request)
//    {
//        if (\Request::isMethod('delete')) {
//            Place::where('id', $request->id)->delete();
//            return ['status' => 'Deleted'];
//        }
//        return abort(404);
//    }
//
//    /**
//     * add new place
//     * check validation and create place
//     * @param Request $request
//     * @return array
//     */
//    public function addPlaces(Request $request)
//    {
//        if ($request->isMethod('post')) {
//            $rules = [
//                'name' => 'required|unique:places',
//            ];
//            $validator = Validator::make($request->record, $rules);
//            if ($validator->fails()) {
//                return response()->json([
//                    'errors' => $validator->errors(),
//                    'status' => 'error'
//                ]);
//            }
//            $place = new Place;
//            $place->name = $request->record['name'];
//            $place->save();
//            $place->subdivision()->sync(isset($request->record['subdivisions']) ? $request->record['subdivisions'] : []);
//            return ($place) ? ['status' => 'success'] : ['status' => 'failed'];
//        }
//        return abort(404);
//    }

    /**
     * @param Request $request
     * @return array|void
     */
    public function updateSubdivision(Request $request){
        if ($request->isMethod('post')) {

            $sub = Subdivision::find($request->record['subdivision'])->first();
            $place = Place::find($request->record['id']);
            $place->name = $request->record['name'];;
            $place->save($sub);
            return ($place) ? ['status' => 'success'] : ['status' => 'failed'];
        }
        return abort(404);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showExpensesPage()
    {
        if (\Request::isMethod('get') && \Request::ajax()) {
            return view('user7.expenses');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function expenseData(Request $request)
    {
        if ($request->isMethod('post')) {
            $place = Expense::select('id', 'name', 'unit')
                ->when($request, function ($query) use ($request) {
                    if ($request->sortBy) {
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('name')
                ->paginate($request->limit)
                ->toArray();
            $data = [
                'records' => $place['data'],
                'total' => $place['total'],
            ];
            return $data;
        }
        return abort(404);
    }

    /**
     * add new expense name
     * check validation and create place
     * @param Request $request
     * @return array
     */
    public function addNewExpense(Request $request)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), ['name' => 'required|unique:expenses']);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => 'error'
                ]);
            }
            Expense::create($request->all());
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * update expense
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateExpense(Request $request)
    {

        if ($request->isMethod('post')) {

            $place = Expense::find($request->record['id'])->update([
                'name' => $request->record['name'],
                'unit' => $request->record['unit'],
            ]);
            if ($place) {
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'failed']);
        }
        return abort(404);
    }

    /**
     * delete expense
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function deleteExpense(Request $request)
    {
        if (\Request::isMethod('delete')) {
            Expense::where('id', $request->id)->delete();
            return ['status' => 'deleted'];
        }
        return abort(404);
    }

    /**
     * show internal destinations page
     * @return array
     * @throws \Throwable
     */
    public function showSuppliersPage()
    {
        if (\Request::isMethod('get') && \Request::ajax()) {
            return view('user7.suppliers');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function suppliersData(Request $request)
    {
        if ($request->isMethod('post')) {
            $suppliers = Supplier::select('id', 'name')
                ->when($request, function ($query) use ($request) {
                    if ($request->sortBy) {
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('name')
                ->paginate($request->limit)
                ->toArray();
            $data = [
                'records' => $suppliers['data'],
                'total' => $suppliers['total'],
            ];
            return $data;
        }
        return abort(404);
    }

    /**
     * update place
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSuppliers(Request $request)
    {

        if ($request->isMethod('post')) {

            $supplier = Supplier::find($request->record['id'])->update([
                'name' => $request->record['name'],
            ]);
            if ($supplier) {
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'failed']);
        }
        return abort(404);
    }

    /**
     * delete place
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function deleteSuppliers(Request $request)
    {
        if (\Request::isMethod('delete')) {
            $supplier = Supplier::find($request->id);
            foreach ($supplier->good as $item) {
                $item->delete();
            }
            $supplier->delete();
            return ['status' => 'Deleted'];
        }
        return abort(404);
    }

    /**
     * add new place
     * check validation and create place
     * @param Request $request
     * @return array
     */
    public function addSuppliers(Request $request)
    {
        if ($request->isMethod('post')) {

            $rules = [
                'name' => 'required|unique:suppliers',
            ];
            $validator = Validator::make($request->record, $rules);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => 'error'
                ]);
            }
            Supplier::create(['name' => $request->record['name']]);
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function showClientPage()
    {
        if (\Request::isMethod('get') && \Request::ajax()) {
            return view('user7.clients');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function clientData(Request $request)
    {
        if ($request->isMethod('post')) {
            $clients = Client::select('id', 'name', 'address', 'phone', 'status')
                ->when($request, function ($query) use ($request) {
                    if ($request->sortBy) {
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('name')
                ->paginate($request->limit)
                ->toArray();
            $data = [
                'records' => $clients['data'],
                'total' => $clients['total'],
            ];
            return $data;
        }
        return abort(404);
    }

    /**
     * add client
     * @param Request $request
     * @return $this|array|\Illuminate\Http\JsonResponse
     */
    public function addClient(Request $request)
    {

        if ($request->isMethod('post')) {
            $client = new Client;
            $client->name = $request->record['name'];
            $client->address = $request->record['address'];
            $client->phone = $request->record['phone'];
            $client->status = $request->record['status'];
            $client->save();

            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * update client
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateClient(Request $request)
    {
        if ($request->isMethod('post')) {

            $clients = Client::find($request->record['id'])->update([
                'name' => $request->record['name'],
                'address' => $request->record['address'],
                'phone' => $request->record['phone'],
                'status' => $request->record['status'],
            ]);

            if ($clients) {
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'failed']);
        }
        return abort(404);

    }

    /**
     * delete client
     * @param Request $request
     * @return $this|array
     * @throws \Exception
     */
    public function deleteClient(Request $request)
    {
        if ($request->isMethod('delete')) {
            Client::find($request->id)->delete();
            return ['status' => 'Deleted'];
        }
        return abort(404);
    }

    /**
     * @param $data
     * @return $this
     */
    public function exportData($data)
    {
        return Excel::create('Պատվերներ', function ($excel) use ($data) {
            $excel->sheet('Պատմություն', function ($sheet) use ($data) {
                $data = array_map(function ($item) {
                    return [
                        'Հաճախորդ' => $item['client_name'],
                        'Ապրանք' => $item['product_name'],
                        'Բոյ' => $item['product_height'],
                        'Գին' => $item['order_price'] / $item['product_amt'],
                        'Քանակ' => $item['product_amt'],
                        'Ընդանուր գումար' => $item['order_price'],
                        'Ամսաթիվ' => $item['date'],
                    ];
                }, $data);
                $sheet->fromArray($data);
            });
        })->export('xls');
    }

    /**
     * @param $data
     * @return $this
     */
    public function exportExpenseHistory($data)
    {

        return Excel::create('Ծախսեր', function ($excel) use ($data) {
            $excel->sheet('Ծախսեր', function ($sheet) use ($data) {
                $data = array_map(function ($item) {
                    return [
                        'Անուն' => (is_null($item['good_name'])) ? $item['expense_name'] : $item['good_name'],
                        'Քանակ' => (is_null($item['good_unit'])) ? $item['amt'] . ' ' . $item['expense_unit'] : $item['amt'] . ' ' . $item['good_unit'],
                        'Գումար' => $item['balance'],
                        'Վճարվել է' => $item['date'],
                        'Ամսվա համար' => $item['month_name'],
                    ];
                }, $data);
                $sheet->fromArray($data);
            });
        })->export('xls');
    }

}
