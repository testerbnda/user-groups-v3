<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Admin\Services\UserService;

class UserController extends Controller
{

    private $userService;
    public function __construct(UserService $userService)
    {
        $this->userService   = $userService;

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin::user.index');
    }

    public function ajaxgetusers()
    { 
        return $this->userService->ajaxgetlist(); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin::user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        \DB::beginTransaction();
        try {
            $data = $request->only('user_type','name', 'email', 'country_code','mobile_no',  'password','status');

            \Log::info($request->getHttpHost().": Create User request received : name=".$data['name'].", mobile_no=".$data['mobile_no'].", email=".$data['email']);
           
              
            $user       = $this->userService->firstornew();
            $result     = $this->userService->save($user, $data);
            

        } catch (\Exception $ex) {
            \DB::rollBack();
            $notification = array(
                'message'    => $ex->getMessage(),
                'alert-type' => 'error',
            );
            \Log::error($request->getHttpHost().": Create User request failed for : email=".$data['email'].", Error=".$ex->getMessage());
            return redirect()->back()->withInput()->with($notification);
        }
        \DB::commit();
        if ($result) {
            $notification = array(
                'message'    => 'You have successfully added user!',
                'alert-type' => 'success',
            );
            \Log::info($request->getHttpHost().": User created Successfully with email =".$data['email'].", user_id=".$result->id);
            return redirect()->route('user.index')->with($notification);
        } else {
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('admin::user.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    { 
        $id     =   encrypt_decrypt('decrypt',$id);
        $user   =   $this->userService->firstornew($id);
 
        
        return view('admin::user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            if (!empty($request->password)) {
                $data = $request->only('user_type','name', 'email', 'country_code','mobile_no',  'password','status');
            } else {
                $data = $request->only('user_type','name', 'email', 'country_code','mobile_no','status');
            }
            \Log::info($request->getHttpHost().": Update User details request received for user_id=".$id);
            $user   = $this->userService->firstornew($id);
            $result = $this->userService->update($user, $data);
            if ($result) {
                $notification = array(
                    'message'    => 'You have successfully updated user!',
                    'alert-type' => 'success',
                );
                \Log::info($request->getHttpHost().": User details updated for user_id=".$id);
                return redirect()->route('user.index')->with($notification);
            } else {
                return redirect()->back()->withInput();
            }

        } catch (\Exception $ex) {
            \Log::error($request->getHttpHost().": Update User details request failed for user_id=".$id." with Error=".$ex->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
