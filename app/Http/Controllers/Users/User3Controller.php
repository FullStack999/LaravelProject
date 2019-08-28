<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Warehouse;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class user3Controller extends Controller
{
    protected $redirectTo = 'user3';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){

        if (\Request::isMethod('get')){
            return view('user3.home');
        }
        return abort(404);
    }

    /**
     * @return array
     */
    public function showPesticidesHistoryPage() {
        if (\Request::isMethod('get') && \Request::ajax()){
            return view('user3.pesticide_history_table');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function pesticidesHistoryData(Request $request){
        $ware = Warehouse::select('warehouses.amt as amt','warehouses.balance as balance',
            'warehouses.updated_at as date','warehouses.good_id as pesticide_id', 'warehouses.comment as comment',
            'goods.name as pesticide_name','goods.unit as pesticide_unit', 'suppliers.name as supplier_name')
            ->join('goods', 'goods.id', '=', 'warehouses.good_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'warehouses.supplier_id')
            ->where('goods.subdivision_id', 3)
            ->whereIn('paid', [1,3])
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
                if ($request->pesticide_id){
                    $query->where('goods.id', $request->pesticide_id);
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


        if ($request->isMethod('get')) {
            $this->exportpesticideHistory($ware->toArray());
        }

        else if($request->isMethod('post')){
            if (!$request->pesticide_id){
                $ware = $ware->groupBy('pesticide_id')->toArray();
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
                $ware->comment = $data['comment'];
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
    public function getPesticides(){
        if (\Request::isMethod('get')){
            return view('user3.pesticides_table');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function pesticidesData(Request $request){
        if ($request->isMethod('post')){
            $pesticides = Warehouse::select('goods.name as good_name', 'goods.id as good_id', 'goods.unit as good_unit',
                'goods.subdivision_id as sub_id', 'warehouses.balance as good_balance', 'warehouses.paid as paid')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->where('goods.subdivision_id', 3)
                ->whereIn('warehouses.paid', [1,3])
                ->when($request, function ($query) use($request){
                    if ($request->sortBy){
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('warehouses.updated_at', 'desc')
                ->get();
            $pesticides = $pesticides->groupBy('good_id');
            $collect = collect();
            foreach($pesticides as $pesticide){
                $collect->push($pesticide[0]);
            }
            return $collect;

        }
        return abort(404);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function accessPesticidesGet() {
        if (\Request::isMethod('get') && \Request::ajax()){
            $view = view('user3.access_table')->render();
            return [
                'view' => $view,
                'data' => $this->accessPesticidesData()
            ];
        }
        return abort(404);
    }

    /**
     * @param bool $id
     * @return \Illuminate\Support\Collection
     */
    public function accessPesticidesData ($id = false){
        if (\Request::isMethod('get') && \Request::ajax()){

            $goods = Warehouse::select('suppliers.name as supplier_name',
                'suppliers.id as supplier_id', 'warehouses.amt as good_amt', 'warehouses.paid as paid', 'goods.id as good_id', 'goods.name as good_name',
                'goods.unit as good_unit', 'warehouses.created_at as date')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'warehouses.supplier_id')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->whereIn('warehouses.paid', [0,2])
                ->where('goods.subdivision_id', 3)
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
    public function returnPesticideToReview (Request $request){
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
    public function confirmPesticide(Request $request){
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
    public function exitPesticidesGet() {
        if (\Request::isMethod('get') && \Request::ajax()){
            return view('user3.exit_table');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function exitPesticidesData(Request $request) {
        if ($request->isMethod('post')){
//            $pesticides = DB::select("select distinct on (good_id) w.balance, w.good_id , g.name, g.unit
//            from warehouses w
//            join goods g ON w.good_id = g.id
//            WHERE g.subdivision_id = 3 AND w.paid = 1
//            ORDER BY w.good_id, w.id DESC");
//            return $pesticides;

            $pesticides = Warehouse::select('goods.name as good_name', 'goods.id as good_id', 'goods.unit as good_unit',
                'goods.subdivision_id as sub_id', 'warehouses.balance as good_balance', 'warehouses.paid as paid')
                ->join('goods', 'goods.id', '=', 'warehouses.good_id')
                ->where('goods.subdivision_id', 3)
                ->whereIn('warehouses.paid', [1,3])
                ->orderBy('warehouses.updated_at', 'desc')
                ->get();
            $pesticides = $pesticides->groupBy('good_id');
            $collect = collect();
            foreach($pesticides as $pesticide){
                $collect->push($pesticide[0]);
            }
            return $collect;
        }
        return abort(404);
    }

    /**
     * @param $data
     * @return $this
     */
    public function exportPesticideHistory($data){
        return Excel::create('Պահեստի շարժ', function($excel) use($data) {
            $excel->sheet('Պահեստի շարժ', function($sheet) use($data) {
                $data = array_map(function ($item){

                    return[
                        'Անուն' => $item['pesticide_name'],
                        'Մուտքեր' => $item['access'],
                        'Ելքեր' => $item['exit'],
                        'Միավոր' => $item['pesticide_unit'],
                        'Մնացորդ' => $item['balance'],
                        'Ամսաթիվ' => $item['date'],
                    ];
                },$data);
                $sheet->fromArray($data);
            });
        })->export('xls');
    }
}
