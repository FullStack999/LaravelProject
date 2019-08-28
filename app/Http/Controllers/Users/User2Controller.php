<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Warehouse;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class User2Controller extends Controller
{
    protected $redirectTo = 'user2';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){

        if (\Request::isMethod('get')){
            return view('user2.home');
        }
        return abort(404);
    }

    /**
     * @return array
     */
    public function showFertilizersHistoryPage() {
        if (\Request::isMethod('get') && \Request::ajax()){
            return view('user2.fertilizer_history_table');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function fertilizersHistoryData(Request $request){
        $ware = Warehouse::select('warehouses.amt as amt','warehouses.balance as balance',
            'warehouses.updated_at as date','warehouses.good_id as fertilizer_id',
            'goods.name as fertilizer_name','goods.unit as fertilizer_unit', 'suppliers.name as supplier_name')
            ->join('goods', 'goods.id', '=', 'warehouses.good_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'warehouses.supplier_id')
            ->where('goods.subdivision_id', 2)
            ->whereIn('warehouses.paid', [1,3])
            ->when( $request,function($query) use($request){
                if ($request->select && $request->select == 'access') {
                    $query->where('warehouses.amt', '>', 0);
                }
                else if ($request->select && $request->select == 'exit'){
                    $query->where('warehouses.amt', '<', 0);
                }
                if ($request->name){
                    $query->where('goods.name','ilike', $request->name.'%');

                }
                if ($request->from){
                    $date_from = Carbon::parse($request->from);
                    $query->where('warehouses.created_at','>', $date_from);
                }
                if ($request->to){
                    $date_to = Carbon::parse($request->to.'24:00:00');
                    $query->where('warehouses.created_at','<', $date_to);
                }
                if ($request->fertilizer_id){
                    $query->where('goods.id', $request->fertilizer_id);
                    $query->orderBy('date', 'desc');
                }
                return $query;
            })->get();
        $ware->map(function($item) {
            if($item->amt < 0){
                $item->exit = abs($item->amt);
                $item->access = '0';
            }else{
                $item->access = $item->amt;
                $item->exit = '0';
            }
        });

        if ($request->isMethod('get')){

            $this->exportFertilizerHistory($ware->toArray());
        }
        else if($request->isMethod('post')){
            $ware->map(function($item) {
                if (is_null($item->place_name)){
                    $item->place_name = $item->sub_name;
                }
                ($item->amt < 0) ? $item->exit = abs($item->amt) : $item->access = $item->amt;

            });
            if (!$request->fertilizer_id){
                $ware = $ware->groupBy('fertilizer_id')->toArray();
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
        if($request->isMethod('post')) {
            foreach ($request->data as $data) {
                $balance = Warehouse::select('balance')
                    ->where('good_id', $data['id'])
                    ->whereIn('paid', [1, 3])
                    ->orderBy('updated_at', 'desc')
                    ->first();
                $ware = new Warehouse;
                $ware->amt = $data['amt'];
                $ware->balance = $balance ? $balance->balance + $data['amt'] : $data['amt'];
                $ware->paid = 1;
                $ware->place()->associate(1);
                $ware->good()->associate($data['id']);
                $ware->save();
            }
            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @return array
     */
    public function getFertilizers(){

        if (\Request::isMethod('get')){
            return view('user2.fertilizers_table');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function fertilizersData(Request $request){
        if ($request->isMethod('post')){
//            $fertilizers = Warehouse::select('goods.id as fertilizer_id','goods.name as fertilizer_name',
//                'goods.unit as fertilizer_unit', DB::raw('SUM(amt) as sum_amt'))
//                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
//                ->join('subdivisions', 'subdivisions.id', '=', 'goods.subdivision_id')
//                ->where('goods.subdivision_id', 2)
//                ->where('warehouses.paid', 1)
//                ->when($request, function ($query) use($request){
//                    if ($request->sortBy){
//                        $query->orderBy($request->sortBy, $request->direction);
//                    }
//                    return $query;
//                })
//                ->orderBy('fertilizer_name', 'asc')
//                ->groupBy('goods.id','goods.name', 'goods.unit','goods.subdivision_id')
//                ->paginate($request->limit)
//                ->toArray();


            $fertilizers = Warehouse::select('goods.name as good_name', 'goods.id as good_id', 'goods.unit as good_unit',
                'goods.subdivision_id as sub_id', 'warehouses.balance as good_balance', 'warehouses.paid as paid')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->where('goods.subdivision_id', 2)
                ->whereIn('warehouses.paid', [1,3])
                ->orderBy('warehouses.updated_at', 'desc')
                ->get();
            $fertilizers = $fertilizers->groupBy('good_id');
            $collect = collect();
            foreach($fertilizers as $fertilizer){
                $collect->push($fertilizer[0]);
            }

            return $collect;
//
//            return [
//                'records' =>  $fertilizers['data'],
//                'total' =>  $fertilizers['total']
//            ] ;

        }
        return abort(404);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function accessFertilizersGet() {
        if (\Request::isMethod('get') && \Request::ajax()){
            return view('user2.access_table');
        }
        return abort(404);
    }

    /**
     * @param bool $id
     * @return \Illuminate\Support\Collection
     */
    public function accessFertilizersData ($id = false){
        if (\Request::isMethod('get') && \Request::ajax()){

            $goods = Warehouse::select('clients.name as client_name', 'clients.id as client_id', 'suppliers.name as supplier_name',
                'suppliers.id as supplier_id', 'warehouses.amt as good_amt', 'warehouses.paid as paid', 'goods.id as good_id', 'goods.name as good_name',
                'goods.unit as good_unit', 'warehouses.created_at as date')
                ->leftJoin('clients', 'clients.id', '=', 'warehouses.client_id')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'warehouses.supplier_id')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->whereIn('warehouses.paid', [0,2])
                ->where('goods.subdivision_id', 2)
                ->whereNotNull('warehouses.supplier_id')
                ->when($id, function($query) use ($id){
                    if ($id){
                        $query->where('suppliers.id', $id);
                    }
                    return $query;
                })->get();

            if (!$id){
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
    public function returnFertilizerToReview (Request $request){
        if ($request->isMethod('post') && $request->ajax()){
            $ware = Warehouse::where([
                'supplier_id' => $request->id,
                'paid' => 0
            ])->update(['paid' => 2]);
            return $ware ? ['status' => 'success'] : ['status' => 'field'];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function confirmFertilizer(Request $request){
        if ($request->isMethod('post') && $request->ajax()) {

            $keys = explode(',', $request->product_keys);
            foreach ($keys as $key) {
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
                $goodsSelect->paid = 1;
                $goodsSelect->balance = is_null($good_balance) ? $goodsSelect->amt : $good_balance->balance + $goodsSelect->amt;
                $goodsSelect->save();
            }

            return ['status' => 'success'];
        }
        return abort(404);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function exitFertilizersGet() {
        if (\Request::isMethod('get') && \Request::ajax()){
            return view('user2.exit_table');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function exitFertilizersData(Request $request) {
        if ($request->isMethod('post')){
//            $fertilizers = DB::select("select distinct on (good_id) w.balance, w.good_id , g.name, g.unit
//            from warehouses w
//            join goods g ON w.good_id = g.id
//            WHERE g.subdivision_id = 2 AND w.paid = 1
//            ORDER BY w.good_id, w.id DESC");

            $fertilizers = Warehouse::select('goods.name as good_name', 'goods.id as good_id', 'goods.unit as good_unit',
                'goods.subdivision_id as sub_id', 'warehouses.balance as good_balance', 'warehouses.paid as paid')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->where('goods.subdivision_id', 2)
                ->whereIn('warehouses.paid', [1,3])
                ->when($request, function ($query) use($request){
                    if ($request->sortBy){
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('warehouses.updated_at', 'desc')
                ->get();
            $fertilizers = $fertilizers->groupBy('good_id');
            $collect = collect();
            foreach($fertilizers as $fertilizer){
                $collect->push($fertilizer[0]);
            }
            return $collect;

        }
        return abort(404);
    }

    /**
     * @param $data
     * @return $this
     */
    public function exportFertilizerHistory($data){
        return Excel::create('Պահեստի շարժ', function($excel) use($data) {
            $excel->sheet('Պահեստի շարժ', function($sheet) use($data) {
                $data = array_map(function ($item){

                    return[
                        'Անուն' => $item['fertilizer_name'],
                        'Միավոր' => $item['fertilizer_unit'],
                        'Մուտքեր' => $item['access'],
                        'Ելքեր' => $item['exit'],
                        'Մնացորդ' => $item['balance'],
                        'Ամսաթիվ' => $item['date'],
                    ];
                },$data);
                $sheet->fromArray($data);
            });
        })->export('xls');
    }
}
