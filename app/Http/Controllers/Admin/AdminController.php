<?php

namespace App\Http\Controllers\Admin;
use Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Type;
use App\User;
use Validator;
use Throwable;

class AdminController extends Controller
{
    protected $redirectTo = '/admin';

    /**
     * show home page
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if(\Request::isMethod('get')) {
            return view('admin.home');
        }
        return abort(404);
    }

    /**
     * get users with there types
     * @param bool $view
     * @return array
     * @throws Throwable
     */
    public function profileGet()
    {
        if(\Request::isMethod('get') && \Request::ajax()) {
            $types = Type::select('id', 'name as text')->get();
            return [
             'view' => view('admin.users_table',['types' => $types])->render(),
             'types' => $types
            ];
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function usersData(Request $request){
        if ($request->isMethod('post')){
            $users = User::select('users.id', 'users.name as name', 'last_name', 'login', 'type_id',
                'created_at', 'updated_at', 'types.name as type_name', 'types.id as type_id')
                ->join('types', 'users.type_id', '=', 'types.id')
                ->when($request, function ($query) use($request){
                    if ($request->sortBy){
                        $query->orderBy($request->sortBy, $request->direction);
                    }
                    return $query;
                })
                ->orderBy('name', 'asc')
                ->get();
            return $users;
        }
        return abort(404);
    }

    /**
     * update user
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function userUpdate(Request $request, User $user)
    {
        if ($request->isMethod('post')){
            $data = $request->except(['created_at', 'updated_at', 'text', 'id']);
            $user->find($request->id)->update($data);
            return ($user) ? ['status' => 'success'] : ['status' => 'failed'];
        }
        return abort(404);
    }

    /**
     * delete user
     * @param Request $request
     * @return array
     * @throws \Exception
     * @throws Throwable
     */
    public function userDelete(Request $request)
    {
        if($request->isMethod('delete')){
            User::find($request->id)->delete();
            return ['status' => 'success'];
        }
        return abort(404);
    }

    public function typesData(){
        $types = Type::select('id', 'name as text');
        return $types;
    }

    /**
     * change user password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileChangePassPost(Request $request){
        if($request->isMethod('post')){
            $user = User::where('id',$request->id)->first();
            if (Hash::check($request->password, $user->password)) {
                return response()->json(["status" => 'failed']);
            }
            else {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json(["status" => 'success']);
            }
        }
        return abort(404);
    }

    /**
     * user registration method
     * check validation and call create method
     * @param Request $request
     * @return array
     */
    public function register(Request $request)
    {
        if($request->isMethod('post')){
            $rules = [
                'email' => 'email|unique:users',
                'login' => 'unique:users',
            ];
            $validator = Validator::make($request->toArray(), $rules);

            if ($validator->fails()) {
                return [
                    'errors'=>$validator->errors(),
                    'status' => 'failed'
                ];
            }
            $user = new User;
            $user->name = $request->name;
            $user->last_name = $request->last_name;
            $user->login = $request->login;
            $user->type()->associate($request->type_id);
            $user->password = bcrypt($request->password);
            $user->save();

            return ['status' => 'success'];
        }
        return abort(404);
    }



}
