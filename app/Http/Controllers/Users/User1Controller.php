<?php

namespace App\Http\Controllers\Users;

use App\Client;
use App\ClientHistory;
use App\Good;
use App\Warehouse;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class User1Controller extends Controller
{
    protected $redirectTo = 'user1';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        if (\Request::isMethod('get')){
            return view('user1.home');
        }
        return abort(404);
    }

    /**
     * @return array
     */
    public function showGoodsHistoryPage() {
        if (\Request::isMethod('get') && \Request::ajax()){
            return view('user1.goods_history_table');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function goodsHistoryData(Request $request){
        $ware = Warehouse::select('warehouses.amt as amt','warehouses.balance as balance',
            'warehouses.updated_at as date_of_updated','warehouses.good_id as good_id',
            'goods.name as good_name','goods.unit as good_unit', 'goods.subdivision_id as sub_id',
            'clients.name as client_name', 'suppliers.name as supplier_name')
            ->join('goods', 'goods.id', '=', 'warehouses.good_id')
            ->leftJoin('clients', 'clients.id', '=', 'warehouses.client_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'warehouses.supplier_id')
            ->where('goods.subdivision_id', 1)
            ->whereIn('warehouses.paid', [1,3])
            ->when( $request,function($query) use($request){

                if ($request->select && $request->select == 'access') {
                    $query->where('warehouses.amt', '>', 0);
                }
                if ($request->history_sections){
                    $query->where('goods.subdivision_id', $request->history_sections);
                }
                else if ($request->select && $request->select == 'exit'){
                    $query->where('warehouses.amt', '<', 0);
                }
                if ($request->name){
                       $query->where('goods.name','ilike', $request->name.'%');
                }
                if ($request->from){
                    $date_from = Carbon::parse($request->from);
                    $query->where('warehouses.updated_at','>', $date_from);
                }
                if ($request->to){
                    $date_to = Carbon::parse($request->to.'24:00:00');
                    $query->where('warehouses.updated_at','<', $date_to);
                }
                if ($request->good_id){
                        $query->where('good_id', $request->good_id);
                    $query->orderBy('date_of_updated', 'desc');
                }
                return $query;
            })->get();


        $ware->map(function($item) {
            if ($item->amt > 0){
                $item->supplier = is_null($item->client_name) ? $item->supplier_name : $item->client_name;
            }
            if($item->amt < 0){
                $item->exit = abs($item->amt);
                $item->access = '0';
            }else{
                $item->access = $item->amt;
                $item->exit = '0';
            }
        });

        if ($request->isMethod('get')){
            $this->exportGoodHistory($ware->toArray());
        }

        else if($request->isMethod('post')){
            $ware->map(function($item) {
                if (is_null($item->place_name)){
                    $item->place_name = $item->sub_name;
                }
                ($item->amt < 0) ? $item->exit = abs($item->amt) : $item->access = $item->amt;
            });
            if (!$request->good_id){
                $ware = $ware->groupBy('good_id')->toArray();
            }

            return $ware;
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function posts(Request $request) {
        if($request->isMethod('post') && $request->ajax()) {

            foreach ($request->data as  $data) {
                $balance = Warehouse::select('balance')
                    ->where('good_id', $data['id'])
                    ->whereIn('paid', [1,3])
                    ->orderBy('updated_at', 'desc')
                    ->first();

                $ware = new Warehouse;
                $ware->amt = $data['amt'];
                $ware->balance = $balance ? $balance->balance + $data['amt'] : $data['amt'];
                $ware->paid = isset($data['client']) ? 3 : 1;
                if (isset($data['client'])){
                    $ware->client()->associate($data['client']);
                }
                else{
                    $ware->place()->associate(2);
                }
                $ware->good()->associate($data['id']);
                $ware->save();

                if (isset($data['type'])){
                    $cl_history = new ClientHistory;
                    $cl_history->client()->associate($data['client']);
                    $cl_history->good_id = $data['id'];
                    $cl_history->good_amt = -$data['amt'];
                    $cl_history->save();
                }
            }
            return response()->json(['status' => 'success']);
        }
        return abort(404);
    }

    /**
     * @return array
     */
    public function getGoods(){
        if (\Request::isMethod('get')){
                return view('user1.goods_table');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function goodsData(Request $request){
        if ($request->isMethod('post')){
            $goods = Warehouse::select('goods.name as good_name', 'goods.id as good_id', 'goods.unit as good_unit',
                'goods.subdivision_id as sub_id', 'warehouses.balance as good_balance', 'warehouses.paid as paid')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->where('goods.subdivision_id', 1)
                ->whereIn('warehouses.paid', [1,3])
                ->when($request, function ($query) use($request){
                    if ($request->sortBy){
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('warehouses.updated_at', 'desc')
                ->get();
            $goods = $goods->groupBy('good_id');
            $collect = collect();
            foreach($goods as $good){
                $collect->push($good[0]);
            }
            return $collect;
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function confirmAccessGood(Request $request){
        if ($request->isMethod('post') && $request->ajax()){

            $keys = explode(',',$request->product_keys);

            foreach ($keys as $key){
                $good_balance = Warehouse::select('balance')
                    ->where('good_id', $key)
                    ->where('paid', '!=', 0)
                    ->orderBy('updated_at', 'desc')
                    ->first();

                $goodsSelect = Warehouse::where([
                        'supplier_id' => $request->id,
                        'paid' => 0,
                        'good_id' => $key
                    ])->first();
                $goodsSelect->paid =  1;
                $goodsSelect->balance = is_null($good_balance) ? $goodsSelect->amt : $good_balance->balance + $goodsSelect->amt;
                $goodsSelect->save();

            }

            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function returnGoodToReview(Request $request){
        if ($request->isMethod('post') && $request->ajax()){
            $ware = Warehouse::where([
                        'supplier_id' => $request->id,
                        'paid' => 0
                    ])
                    ->update(['paid' => 2]);
            return $ware ? ['status' => 'success'] : ['status' => 'field'];
        }
        return abort(404);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function accessGoodGet() {
        if (\Request::isMethod('get') && \Request::ajax()){
            $view = view('user1.access_table')->render();
            return ['view' => $view,];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function accessGoodData (Request $request){

        if ($request->isMethod('post') && $request->ajax()){

            $goods = Warehouse::select('suppliers.name as supplier_name', 'suppliers.id as supplier_id',
                'warehouses.amt as good_amt', 'warehouses.paid as paid', 'goods.id as good_id',
                'goods.name as good_name', 'goods.unit as good_unit', 'warehouses.created_at as date_of_created')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'warehouses.supplier_id')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->whereIn('warehouses.paid', [0,2])
                ->whereNotNull('suppliers.id')
                ->where('goods.subdivision_id', 1)
                ->when($request, function ($query) use($request){
                    if ($request->id){
                        $query->where('suppliers.id', $request->id);
                    }
                    return $query;
                })
                ->get();
            if (!$request->id){
                $goods = $goods ->groupBy('supplier_id');
            }
            return $goods;
        }
        return abort(404);

    }

    /**
     * @param Request $request
     * @return array
     */
    public function receptionGoodData (Request $request){

        if ($request->isMethod('post') && $request->ajax()){
            $goods = Good::select('id', 'name', 'unit')
                ->where([
                    'goods.subdivision_id' =>  1,
                    'returnable' => 1
                ])
                ->get();
            $clients = Client::select('id', 'name')
                ->get();

            return ['goods' => $goods, 'clients' => $clients];
        }
        return abort(404);

    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function exitGoodGet() {
        if (\Request::isMethod('get') && \Request::ajax()){
            return view('user1.exit_table');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function exitGoodPost(Request $request) {
        if ($request->isMethod('post')){
//            $goods = DB::select("select distinct on (good_id) w.balance, w.good_id, w.paid, g.name, g.unit, g.subdivision_id
//            from warehouses w
//            join goods g ON w.good_id = g.id
//            where g.subdivision_id = 1 AND w.paid in(1,3)
//            order by w.updated_at desc");

            $goods = Warehouse::select('goods.name as good_name', 'goods.id as good_id', 'goods.unit as good_unit',
                'goods.subdivision_id as sub_id', 'warehouses.balance as good_balance', 'warehouses.paid as paid')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->where('goods.subdivision_id', 1)
                ->whereIn('warehouses.paid', [1,3])
                ->when($request, function ($query) use($request){
                    if ($request->sortBy){
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('warehouses.updated_at', 'desc')
                ->get();
            $goods = $goods->groupBy('good_id');
            $collect = collect();
            foreach($goods as $good){
                $collect->push($good[0]);
            }
            return $collect;
        }

        return abort(404);
    }

    /**
     * @param $data
     * @return $this
     */
    public function exportGoodHistory($data){
        return Excel::create('Պահեստի շարժ', function($excel) use($data) {
            $excel->sheet('Պահեստի շարժ', function($sheet) use($data) {
                $data = array_map(function ($item){

                    return[
                        'Անուն' => $item['good_name'],
                        'Մուտքեր' => $item['access'],
                        'Ելքեր' => $item['exit'],
                        'Միավոր' => $item['good_unit'],
                        'Մնացորդ' => $item['balance'],
                        'Ամսաթիվ' => $item['date'],
                    ];
                },$data);
                $sheet->fromArray($data);
            });
        })->export('xls');
    }

}
