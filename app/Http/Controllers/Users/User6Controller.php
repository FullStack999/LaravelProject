<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Product;
use DB;
use Illuminate\Http\Request;
use App\EndProduct;
use App\OrdersNumber;
use App\Order;
use App\Client;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Validator;

class User6Controller extends Controller
{
    protected $redirectTo = 'user6';
    protected $bool;

    /**
     * return home page
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        if(\Request::isMethod('get')){

            return view('user6.home');
        }
        return back()->withErrors(['status','Something Went Wrong']);
    }

    /**
     * return orders page
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addOrdersPage(){
        if(\Request::isMethod('get') && \Request::ajax()) {
            $client = Client::select('id', 'name', 'status')->get();
            return view('user6.order_registerer_table', ['client' => $client]);
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function getOrderData(Request $request) {
        if ($request->isMethod('post')){
            $products = DB::select("select distinct on (product_id) e.product_id, e.balance,
            p.local_price, p.export_price, p.name, p.height
            from end_products e
            join products p ON e.product_id = p.id
            ORDER BY e.product_id, e.id DESC");


            return $products;
        }
        return abort(404);
    }

    /**
     * set orders table
     * @param Request $request
     * @return array
     */
    public function addOrders(Request $request){
        if ($request->isMethod('post')){
            return ($this->newOrder($request['data'])) ? ['status' => 'success'] : ['status' => 'field'];
        }
        return abort(404);
    }

    /**
     * @param $req
     * @return bool
     */
    public function discountOrder($req){
        foreach ($req as $data){
            if ($data['old_price'] != $data['price']){
                return true;
            }
        }
        return false;
    }

    /**
     * @param $req
     * method add order
     * @return bool
     */
    public function newOrder($req){

        $discount = $this->discountOrder($req);
        $not_enough = $this->addOrderValidation($req);
        $orders_number = new OrdersNumber;
        $orders_number->confirmed = ($discount == false)? 1 : 0;
        if ($not_enough != ''){
            $orders_number->not_enough = $not_enough;
        }
        $orders_number->client()->associate($req[0]['client_id']);
        $orders_number->save();

        foreach ($req as $data){
            $order = new Order;
            $order->amt = $data['amt'];
            $order->price = $data['price'] * $data['amt'];
            if ($data['old_price'] != $data['price']){
                $order->discount_price = $data['price'];
            }
            $order->product()->associate($data['id']);
            $order->ordersNumber()->associate($orders_number);
            $order->save();

            $balance = EndProduct::select('product_id', 'balance')
                ->where('product_id', $data['id'])
                ->orderBy('updated_at', 'desc')
                ->first();

            $product = new EndProduct;
            $product->amt = -$data['amt'] ;
            $product->balance = ($balance) ? $balance->balance - $data['amt'] : -$data['amt'];
            $product->product()->associate($data['id']);
            $product->ordersNumber()->associate($orders_number);
            $product->save();
        }
        return true;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function addOrderExcel(Request $request){

        if ($request->isMethod('post')){
            if ($request->hasFile('excel')) {
                $validate = Validator::make([
                    'file' => $request->excel,
                    'client_id' => $request->client_id,
                ],
                    [
                        'file' => 'required|mimes:xlsx,xls',
                        'client_id' => 'required',
                    ]);

                if ($validate->fails()) {
                    return response()->json([
                        'errors' => $validate->errors(),
                        'status' => 'failed']);
                }
                Excel::load($request->excel->path(), function ($reader) use ($request) {
                    $client = Client::find($request->client_id);
                    $products = Product::select('id')
                        ->when($client, function($query) use($client){
                            if($client->status == 0){
                                $query->addSelect('local_price as price');
                            }else{
                                $query->addSelect('export_price as price');
                            }
                        })->get();
                    $collect = collect();
                    foreach ($reader->toArray() as $item){
                        if ($item['քանակ'] != '' && $item['քանակ'] != 0){
                            $old_price = $products->where('id', $item['id'])->first();
                            $collect->push([
                                'client_id' => $request->client_id,
                                'id' => $item['id'],
                                'amt' => $item['քանակ'],
                                'price' => $item['գին'],
                                'old_price' => $old_price->price
                            ]);
                        }
                    }
                    $this->newOrder($collect->toArray());

                });
            }else{
                return ['status' => 'failed'];
            }
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function sendExcelFile(Request $request){

        if ($request->isMethod('post')){
            if ($request->hasFile('file')){

                $validate = Validator::make([
                    'file' => $request->file,
                    'email' => $request->email,
                ],
                    [
                        'file' => 'required|mimes:xlsx,xls',
                        'email' => 'required|email',
                    ]);

                if ($validate->fails()) {
                    return response()->json([
                        'errors' => $validate->errors(),
                        'status' => 'failed']);
                }

                Mail::send('mail', [],function ($message) use ($request){
                    $message->to($request->email)->subject('Ապրանքների ցուցակ');
                    $message->attach($request->file('file')->getRealPath(), [
                        'as' => 'Ապրանքներ.' . $request->file('file')->getClientOriginalExtension(),
                        'mime' => $request->file('file')->getMimeType()
                    ]);

                });
                return ['status' => 'success'];
            }
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function uploadExcelFile(Request $request){

        if ($request->isMethod('get')){
            if ($request->client != ''){
                $user = Client::find($request->client);
                $products = EndProduct::select('products.id as id',
                    'products.name as name', 'products.height as height', 'end_products.balance as balance', 'end_products.amt as amt')
                    ->join('products', 'products.id', '=', 'end_products.product_id')
                    ->when($user, function ($query) use($user){
                        if ($user->status == 0){
                            $query->addSelect('products.local_price as price');
                        }
                        else{
                            $query->addSelect('products.export_price as price');
                        }
                        return $query;

                    })->get();

                $collect = $products->groupBy('id');
                $collection = collect();
                foreach ($collect as $key => $item){
                     $collection->push($products->where('id',$key)->last());
                }

                Excel::create('Ապրանքներ', function($excel) use($collection) {
                    $excel->sheet('Ապրանքներ', function($sheet) use($collection) {
                        $collection = array_map(function ($item){
                            return[
                                'id' => $item['id'],
                                'Անուն' => $item['name'],
                                'Բոյ' => $item['height'],
                                'Գին' => $item['price'],
                                'Մնացորդ' => $item['balance'],
                            ];
                        },$collection->toArray());
                        $sheet->getColumnDimension('A')->setVisible(false);
                        $sheet->setCellValue('F1', 'Քանակ');
                        $sheet->fromArray($collection);
                    });
                })->export('xls');
            }else{
                return back()->withErrors(['status','Something Went Wrong']);
            }
        }
        return abort(404);
    }

    /**
     * @param $req
     * @return array|bool
     */
    public function addOrderValidation($req){
        $not_enough = '';
        foreach ($req as $item => $value){
            $balance = EndProduct::select('balance', 'product_id')
                ->where('product_id', $value['id'])
                ->orderBy('updated_at', 'desc')
                ->first();
            $bal = ($balance) ? $balance->balance : 0;
            if ($bal - $value['amt'] < 0){
                $not_enough .= ($not_enough == '') ? $balance->product_id : ','.$balance->product_id;
            }
        }
        return $not_enough;
    }

    /**
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ordersPage(){

        if (\Request::isMethod('get') && \Request::ajax()) {
            return view('user6.orders');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return User7Controller
     */
    public function ordersData(Request $request){

        if ($request->isMethod('post')){

            $orders = OrdersNumber::select('orders_number.id as id', 'orders_number.exit_ware_status as exit_ware_status', 'orders_number.not_enough as not_enough', 'orders_number.exit_ware_status as exit_ware_status' , 'orders_number.confirmed as confirmed',
                'clients.id as client_id','clients.name as client_name', 'clients.status as client_status', 'products.id as product_id',
                'products.name as product_name','products.height as product_height', 'products.height as product_height',
                'products.local_price as local_price', 'products.export_price as export_price', 'orders.amt as product_amt',
                'orders.price as order_price', 'orders.discount_price as discount_price','orders.created_at as date','orders.id as order_id')
                ->join('orders','orders.orders_number_id','=','orders_number.id')
                ->join('products','products.id','=','orders.product_id')
                ->join('clients','clients.id','=','orders_number.client_id')
                ->when($request, function ($query) use($request){
                    if ($request->from){
                        $date_from = Carbon::parse($request->from);
                        $query->where('orders.created_at', '>', $date_from);
                    }
                    if ($request->to){
                        $date_to = Carbon::parse($request->to.'24:00:00');
                        $query->where('orders.created_at', '<', $date_to);
                    }
                    if ($request->id){
                        $query->where('orders_number.id', $request->id);
                    }
                    if ($request->name){
                        $query->where('clients.name','ilike',  $request->name."%");
                    }
                    $query->orderBy('product_name');
                    return $query;
                })->get();
            if (!$request->has('id')){
                $orders = $orders->groupBy('id');
            }
            return $orders;
        }

        return abort(404);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function backFillOrders(){

        if(\Request::isMethod('get') && \Request::ajax()) {
            $order_number = OrdersNumber::select('orders_number.id as id', 'clients.name as client_name',
                'clients.id as client_id', 'clients.status as status')
                ->where([
                    'orders_number.exit_ware_status' => 0,
                    'orders_number.confirmed' => 1,
                    'orders_number.back_fill' => 1,
                ])
                ->join('clients', 'clients.id', '=', 'orders_number.client_id')
                ->get();
            $products = Product::select('id', 'name', 'height')->get();
            $products->map(function ($item){

            });
            return view('user6.back_fill_orders', ['order_number' => $order_number, 'products' => $products]);
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function backFillOrdersData(Request $request){
        if ($request->isMethod('post')){
            $data = OrdersNumber::select('orders_number.id as id', 'orders_number.client_id as client_id',
                'products.id as product_id', 'products.name as product_name', 'products.height as product_height',
                'orders.discount_price as discount_price', 'orders.price as order_price', 'orders.amt as product_amt','clients.name as client_name',
                'clients.status as client_status', 'products.local_price as local_price',
                'products.export_price as export_price')
                ->join('orders','orders.orders_number_id','=','orders_number.id')
                ->join('products','products.id','=','orders.product_id')
                ->join('clients','clients.id','=','orders_number.client_id')
                ->where('orders_number.id', $request->id)
                ->get();
            $products = Product::select('id', 'name', 'height', 'local_price', 'export_price')->orderBy('name', 'asc')->get();

            return ['data' => $data, 'products' => $products];
        }
        return abort(404);
    }

    public function backFillNewProduct(Request $request){
        if ($request->isMethod('post')){
            $client = Client::find($request->client_id);
            $product = Product::find($request->prod_id);

            return [
                'price' => ($client->status == 0) ? $product->local_price : $product->export_price,
                'prod_id' => $product->id
            ];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function updateBackFill(Request $request){

        if ($request->isMethod('post')){
            if (!is_null($request->records[0]['order_id'])){

                $insert = $this->newOrder($request->records);
                if ($insert){
                    OrdersNumber::find($request->records[0]['order_id'])->delete();
                    return ['status' => 'success'];
                }else{
                    return ['status' => 'fail'];

                }

            }
            return ['status' => 'failed'];

        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function deleteOrder(Request $request){
        if ($request->isMethod('delete')){
            OrdersNumber::find($request->id)->delete();
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function deleteDetailOrder(Request $request){
        if ($request->isMethod('delete')){
            Order::find($request->id)->delete();
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     */
    public function updateDetailOrder(Request $request){
        if ($request->isMethod('post')){
            $data = [];
            $not_enough = null;
            array_push($data, [
                'id' => $request->product_id,
                'price' => str_replace('.', '',$request->discount_price),
                'old_price' => str_replace('.', '',$request->price),
                'amt' => $request->product_amt
            ]);


            $orders_number = OrdersNumber::find($request->id);
            $end_product = EndProduct::where([
                'orders_number_id' => $request->id,
                'product_id' => $request->product_id,
            ])->orderBy('updated_at', 'desc')->first();

            $balance = EndProduct::select('balance')->where('product_id', $request->product_id)
                ->orderBy('updated_at', 'desc')
                ->first();
            $end_product->balance = $balance->balance - ($end_product->amt + $request->product_amt);
            $end_product->amt = -$request->product_amt;

            $order = Order::find($request->order_id);

            $order->amt = $request->product_amt;
            $order->price = $request->product_amt * str_replace('.', '',$request->price);
            if ($request->discount_price == 0 || is_null($request->discount_price)){
                $orders_number->confirmed = 1;
                $order->discount_price = 0;
                $order->price = $request->product_amt * str_replace('.', '',$request->price);
            }
            else {
                $orders_number->confirmed = 0;
                $order->discount_price = str_replace('.', '',$request->discount_price);
                $order->price = $request->product_amt * str_replace('.', '', $request->discount_price);
            }

            if (!is_null($orders_number->not_enough)){
                $explode = explode(',', $orders_number->not_enough);
            }else{
                $explode = [];
            }

            if ($end_product->balance < 0 && !in_array($request->product_id, $explode)){
                array_push($explode, $request->product_id);
                $orders_number->not_enough = implode($explode, ',');
            }
            else if($end_product->balance >= 0 && in_array($request->product_id, $explode)){
                array_splice($explode,array_search($request->product_id, $explode), 1);
                if (empty($explode)){
                    $orders_number->not_enough = null;
                }else{
                    $orders_number->not_enough = implode($explode, ',');
                }
            }
            $end_product->save();
            $orders_number->save();
            $order->save();
        }
    }

}
