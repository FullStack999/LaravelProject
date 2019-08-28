<?php
namespace App\Http\Controllers\Users;
use App\Good;
use App\Http\Controllers\Controller;

use App\ClientHistory;
use App\EndProduct;
use App\OrdersNumber;
use App\Product;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class User4Controller extends Controller
{
    protected $redirectTo = 'user4';

    /**
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        if (\Request::isMethod('get')) {
            return view('user4.home');
        }
        return abort(404);
    }

    /**
     * @return $this|array
     * @throws \Throwable
     */
    public function ordersGet() {
        if (\Request::isMethod('get') && \Request::ajax()){

            return view('user4.orders');
        }
        return abort(404);
    }

    /**
     * @return $this|array|\Illuminate\Support\Collection
     */

    public function ordersData(){
        if (\Request::isMethod('get')){
            $ordersNumber = OrdersNumber::select('orders_number.id as id', 'orders_number.back_fill as back_fill',
                'orders_number.created_at as date','clients.id as client_id',
                'clients.name as client_name')
                ->join('clients','clients.id','=','orders_number.client_id')
                ->whereNull('orders_number.not_enough')
                ->where([
                    'orders_number.exit_ware_status' => 0,
                    'orders_number.confirmed' => 1,
                ])->get();
            $ordersNumber = $ordersNumber->groupBy('id')->toArray();

            $goods = Good::select('id', 'name')->where([
                'subdivision_id' => 1,
                'returnable' => 1
            ])->get();

            return [
                'orders' => $ordersNumber,
                'goods' => $goods
            ];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Support\Collection
     */
    public function orderDetail(Request $request){
        if ($request->isMethod('post')){
            $data= OrdersNumber::select('orders_number.id as id','products.id as product_id',
                'products.name as product_name', 'products.height as product_height',
                DB::raw('SUM(orders.amt) as product_amt'))
                ->join('orders','orders.orders_number_id','=','orders_number.id')
                ->join('products','products.id','=','orders.product_id')
                ->where('orders_number.id', $request->id)
                ->when($request, function($query) use($request){
                    if ($request->sortBy){
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->groupBy('products.id','orders_number.id')
                ->get();

            return $data;
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function ordersPost(Request $request){
        if ($request->isMethod('post')){
                $order_number = OrdersNumber::select('client_id', DB::raw('sum(orders.price) as debt'))
                    ->where([
                        'client_id' => $request->data[0]['client_id'],
                        'exit_ware_status' => 0,
                        'orders_number.confirmed' => 1,
                    ])
                    ->join('orders', 'orders_number.id', '=', 'orders.orders_number_id')
                    ->groupBy('client_id')
                    ->get();

            foreach ($request->data as $item){
                $client = new ClientHistory;
                $client->client()->associate($item['client_id']);
                $client->debt = $order_number[0]['debt'];
                $client->good_id = $item['good_id'];
                $client->good_amt = $item['good_amt'];
                $client->save();
                $order_number[0]['debt'] = 0;
            }

            OrdersNumber::find($request->data[0]['id'])
                ->update(['exit_ware_status' =>  1]);
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function backFill(Request $request){
        if ($request->isMethod('post')){
            $orde = OrdersNumber::find($request->id)->update(['back_fill' => 1]);
            if ($orde){
                return ['status' => 'success'];
            }
            return ['status' => 'field'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|string
     */
    public function addProductsPage(Request $request) {
        if (\Request::isMethod('get') && \Request::ajax()){
            $products = Product::select('id','name','height')
                ->when($request, function ($query) use($request){
                    if ($request->sortBy){
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('name','asc')
                ->get();
            $products = $products->groupBy('name');
            return view('user4.add_products',['products' => $products]);
        }
        return abort(404);
    }

    /**
     * @param Request $request
     */
    public function addProductData(Request $request){
        if ($request->isMethod('post')){
            $products = Product::select('id','name','height')
                ->when($request, function ($query) use($request){
                    if ($request->sortBy){
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('name','asc')
                ->get();
            $products = $products->groupBy('name');
            return $products;
        }
        return abort(404);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function getProducts()
    {
        if (\Request::isMethod('get') && \Request::ajax()){
            $end_products = DB::select(" select distinct on (product_id) e.*, p.name,p.height
            from end_products e
            join products p ON e.product_id = p.id
            ORDER BY e.product_id, e.id DESC ");
            return [
                'view' => view('user4.products')->render(),
                'end_products' =>  $end_products
            ];
        }
        return abort(404);
    }

    /**
     * @return $this|string
     */
    public function getMovementsHistory() {
        if (\Request::isMethod('get') && \Request::ajax()){
            $clients = OrdersNumber::select( 'client_id as client_id', 'clients.name as client_name')
                ->join('clients', 'clients.id', '=', 'orders_number.client_id')
                ->where('orders_number.exit_ware_status', 1)
                ->groupBy('client_id', 'client_name')
                ->get();

            return view('user4.internal_movements_table', ['clients' => $clients]);
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function movementsHistoryData(Request $request) {

        $move = EndProduct::select('amt','balance',
            'product_id', 'end_products.created_at as date',
            'products.name as product_name','products.height as product_height', 'orders_number.client_id as client_id',
            'orders_number.exit_ware_status as exit_ware_status')
            ->join('products', 'products.id', '=', 'end_products.product_id')
            ->leftJoin('orders_number', 'orders_number.id', '=', 'end_products.orders_number_id')
            ->when($request ,function($query) use($request){
                if ($request->sortBy){
                    $query->orderBy($request->sortBy, $request->direction);
                }
                if ($request->select && $request->select == 'access') {
                    $query->where('amt', '>', 0);
                }
                else if ($request->select && $request->select == 'exit'){
                    $query->where('amt', '<', 0);
                }
                if ($request->client_id){
                    $query->where('orders_number.client_id', $request->client_id);
                    $query->where('orders_number.exit_ware_status', 1);
                }
                if ($request->from){
                    $date_from = Carbon::parse($request->from);
                    $query->where('end_products.created_at','>', $date_from);
                }
                if ($request->to){
                    $date_to = Carbon::parse($request->to.'24:00:00');
                    $query->where('end_products.created_at','<', $date_to);
                }
                if ($request->product_id){
                    $query->where('product_id', $request->product_id);

                }

                return $query;
            })->orderBy('date', 'desc')->get();

        if ($request->isMethod('get')){
            $move->map(function ($item){
                if ($item->amt > 0){
                    $item->access = $item->amt;
                    $item->exit = 0;
                }else{
                    $item->exit = $item->amt;
                    $item->access = 0;
                }
            });
            return $this->exportExpenseHistory($move->toArray());
        }
        if ($request->isMethod('post')){
            $move->map(function ($item){
                if ($item->amt > 0){
                    $item->access = $item->amt;
                    $item->exit = 0;
                }else{
                    $item->exit = $item->amt;
                    $item->access = 0;
                }
            });
            if (!$request->product_id){
                $move = $move->groupBy('product_id');
            }
            return $move;
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function addEndProducts(Request $request)
    {
        if ($request->isMethod('post')){

            foreach ($request->data as $data){

                $balance = EndProduct::select(DB::raw('SUM(amt) as total'))
                    ->where('product_id',$data['product_id'])
                    ->get();
                $end_product = new EndProduct;
                $end_product->amt =$data['product_amt'];
                $end_product->balance = $balance[0]->total + $data['product_amt'];
                $end_product->product()->associate($data['product_id']);
                $end_product->save();
            }
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @param $data
     * @return $this
     */
    public function exportExpenseHistory($data){

        return Excel::create('Ներքին շարժ', function($excel) use($data) {
            $excel->sheet('Ներքին շարժ', function($sheet) use($data) {
                $data = array_map(function ($item){
                    return[
                        'Անուն' => $item['product_name'].' '.$item['product_height'],
                        'Ելքեր' => $item['exit'],
                        'Մուտքեր' => $item['access'],
                        'Մնացորդ' => $item['balance'],
                        'Ամսաթիվ' => $item['date'],
                    ];
                },$data);
                $sheet->fromArray($data);
            });
        })->export('xls');
    }
}
