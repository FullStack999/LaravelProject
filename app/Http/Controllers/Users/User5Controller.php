<?php

namespace App\Http\Controllers\Users;

    use App\Client;
    use App\Expense;
    use App\Good;
    use App\Http\Controllers\Controller;
    use App\OrdersNumber;
    use App\Place;
    use App\Product;
    use App\Supplier;
    use Auth;
    use Illuminate\Support\Carbon;
    use Illuminate\Http\Request;

    class User5Controller extends Controller
    {
        protected $redirectTo = 'user5';

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function index(){
            return view('user5.home');
        }

        /**
         * @return array
         * @throws \Throwable
         */

        public function notifications(){
            if (\Request::isMethod('get') && \Request::ajax()){
                if (Auth::user()->type_id == 6){
                    $notify = OrdersNumber::select('orders_number.id as order_id','orders_number.created_at as date',
                        'clients.name as client_name', 'orders_number.confirmed as confirmed',
                        'orders_number.not_enough as not_enough')
                        ->whereNotNull('not_enough')
                        ->orWhere('confirmed', 0)
                        ->join('clients', 'clients.id', '=', 'client_id' )
                        ->get();
                    return  [
                        'view' => view('layouts.notifications',['notify' => $notify])->render(),
                        'notify_count' => count($notify),
                        'user' => Auth::user()->type_id,
                    ];
                }
                return ['status' => 'field'];
            }
            return abort(404);
        }
        /**
         * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function showReceivingOrdersPage(){

            if (\Request::isMethod('get') && \Request::ajax()) {
                return view('user5.orders');
            }
            return abort(404);
        }

        /**
         * @param Request $request
         * @return array
         */
        public function dataReceivingOrder(Request $request){

            if ($request->isMethod('post')){

                $orders = OrdersNumber::select('orders_number.id as id', 'orders_number.not_enough as not_enough', 'orders_number.confirmed as confirmed',
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
                        if ($request->notify_id){
                            $query->where('orders_number.id', $request->notify_id);
                        }
                        return $query;
                    })->orderBy('date', 'desc')->get();


                if (!$request->has('id')){
                    $orders = $orders->groupBy('id')->toArray();
                }

                return $orders;
            }
            return abort(404);
        }

        /**
         * @param Request $request
         * @return array
         */
        public function confirmOrder(Request $request){
            if ($request->isMethod('post')){
                OrdersNumber::find($request->id)->update([
                    'confirmed' => 1,
                    'not_enough' => null,
                ]);
                return ['status' => 'success'];
            }
            return abort(404);
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function deleting(){
            if (\Request::isMethod('get') && \Request::ajax()){
                return view('user5.deleting');
            }
            return abort(404);
        }

        /**
         * @param Request $request
         */
        public function deletingData(Request $request){
            if ($request->isMethod('post') && $request->ajax()){
                switch ($request->table){
                    case 'clients': return Client::onlyTrashed()->get(); break;
                    case 'suppliers': return Supplier::onlyTrashed()->get(); break;
                    case 'places': return Place::onlyTrashed()->get(); break;
                    case 'goods': return Good::onlyTrashed()->get(); break;
                    case 'products': return Product::onlyTrashed()->get(); break;
                    case 'expenses': return Expense::onlyTrashed()->get(); break;
                }
            }
            return abort(404);
        }

        public function removing(Request $request){
            if ($request->isMethod('post') && $request->ajax()){
                switch ($request->table){
                    case 'client':
                        $client = Client::withTrashed()->find($request->id)->forceDelete();
                        return ($client) ? ['status' => 'success'] : ['status' => 'field'];
                        break;
                    case 'supplier':
                        $suppliers = Supplier::withTrashed()->find($request->id)->forceDelete();
                        return ($suppliers) ? ['status' => 'success'] : ['status' => 'field'];
                        break;
                    case 'place':
                        $places = Place::withTrashed()->find($request->id)->forceDelete();
                        return ($places) ? ['status' => 'success'] : ['status' => 'field'];
                        break;
                    case 'good':
                        $good= Good::withTrashed()->find($request->id)->forceDelete();
                        return ($good) ? ['status' => 'success'] : ['status' => 'field'];
                        break;
                    case 'product':
                        $product = Product::withTrashed()->find($request->id)->forceDelete();
                        return ($product) ? ['status' => 'success'] : ['status' => 'field'];
                        break;
                    case 'expense':
                        $expense = Expense::withTrashed()->find($request->id)->forceDelete();
                        return ($expense) ? ['status' => 'success'] : ['status' => 'field'];
                        break;
                }
            }
            return abort(404);
        }


    }
