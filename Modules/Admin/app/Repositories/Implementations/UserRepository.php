<?php

namespace Modules\Admin\Repositories\Implementations;
 
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;
use Modules\Core\Helpers\BasicHelper;
use Modules\Core\Helpers\Logger;   
use Modules\Admin\Repositories\Interfaces\UserInterface;
use PDF; 
use Symfony\Component\HttpFoundation\Response;

class UserRepository implements UserInterface
{
    /**
     * Get new instance
     */ 

    public function save(User $user, array $data)
    {
        if (isset($data['password'])) {
            $password = $data['password'];
        } else {
            $password = BasicHelper::randomStrings(6);
        }
 
          $site_id = get_loggedin_user_siteid();

        

        if (isset($data['user_type'])) {
            $user_type = $data['user_type'];
        } else {
            $user_type = 'user';
        }

        \DB::beginTransaction();
        try {

            $user = User::create([
                'name'         => $data['name'],
                'email'        => $data['email'],
                'country_code' => isset($data['country_code']) ? $data['country_code'] : 91,
                'mobile_no'    => $data['mobile_no'],
                'password'     => $password
            ]);

            DB::table('site_users')->insertGetId([
                'user_id' => $user->id,
                'site_id' => $site_id,
                'status' => 1,
                'role' => $user_type
            ]);


            

            \DB::commit();

        } catch (Exception $ex) {
            \DB::rollback();
            Logger::error($ex);
        }
        
        return $user;
    }

        public function updatedetail(User $user, array $data)
    {
         $user->update($data);
         return $user;
    }

    public function update(User $user, array $data)
    {
       
        $site_id = get_loggedin_user_siteid();
        $user_type = $data['user_type'];
        
        unset($data['user_type']);
        $res =  $user->update($data); 

        DB::table('site_users')->where('user_id')->where('site_id',$site_id)->update([
            'role' => $user_type
        ]);

        return $res;
    }

    public function firstornew($id)
    {
        if (!empty($id)) {
            $userData =  User::join('site_users','site_users.user_id','users.id')->select('users.*','site_users.role')->where('users.id', $id)->first();
 
            return $userData;
        }
        return new User();
    }

    public function ajaxgetlist()
    {
        
        if (request()->ajax()) {
            $site_id = get_loggedin_user_siteid();
            
            $data = User::leftJoin('site_users', 'site_users.user_id', '=', 'users.id')
                        ->where('site_users.site_id', $site_id)
                        ->select('users.*')
                        ->where('users.id', '!=', Auth::user()->id)
                        ->orderBy('users.id', 'DESC');
                        //->groupby('users.id')
 
            
            return datatables()->eloquent($data)
                ->setRowClass(function ($request) {
                    return $request->status == 1 ? 'nk-tb-item' : 'nk-tb-item font-italic';
                })
                ->editColumn('created_at', function ($request) {
                    return $request->created_at->format('d/m/Y H:i:s');
                }) 

                ->editColumn('name', function ($request) {
                     
                    return '<div class="user-card"><div class="user-info"><span class="tb-lead">' . $request->name . '<span class="dot dot-success d-md-none ml-1"></span></span></div></div>';
                         
                })
                
                ->editColumn('mobile_no', function ($request) {
                    if(isset($request->mobile_no)){
                        return  '<span> +' . $request->country_code . '&nbsp;' . $request->mobile_no . '</span>';
                    } else{ return '<span>-</span>'; }
                })
                ->editColumn('status', function ($request) {
                    if ($request->status == 1) {
                         return '<label class="custom-control-label" for="customSwitch' . $request->id . '">Active</label>';
                    } else {
                        return '<label class="custom-control-label" for="customSwitch' . $request->id . '">Inactive</label>';
                    }
                })
                
                ->addColumn('action', function ($data) {

                          return $link =  '<div class="demo-inline-spacing">
                                                <a href="' . url('admin/user/edit', encrypt_decrypt('encrypt',$data->id)) . '" ><button type="button" class="btn btn-icon btn-primary">
                                                <span class="tf-icons bx bx-pencil bx-22px"></span>
                                                </button></a>
                                                
                                            
                                            </div>';
                    

                   // return $link;
                })
                ->rawColumns(['action', 'created_at', 'name', 'mobile_no','status'])
                ->make(true);
        }
    }
 
} //end
