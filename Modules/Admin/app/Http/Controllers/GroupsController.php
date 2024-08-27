<?php

namespace Modules\Admin\Http\Controllers;
use Modules\Admin\Services\GroupService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Helpers\Logger;
use Modules\Admin\Http\Requests\GroupUpdateService;


class GroupsController extends Controller
{
    private $groupService;

    // Constructor
    public function __construct(GroupService $groupService) {
        $this->groupService = $groupService;  
    }

    /**
     * Display a listing of the resource.   
     */
    public function index() {
        return view('admin::groups.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return view('admin::groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        //
        try {
            $data = $request->validate([
                'name' => 'required|string|max:222',
                'status' => 'required|integer|between:0,255',
                'site_id' => 'required|exists:sites,id',
                'user_ids' => 'nullable|string', 
            ]);
    
            $userIds = $request->input('user_ids');
            $userIdsArray = !empty($userIds) ? explode(',', $userIds) : [];
    
            return $this->groupService->createGroup([
                'name' => $data['name'],
                'status' => $data['status'],
                'site_id' => $data['site_id'],
                'user_ids' => $userIdsArray,
            ]);
        } catch (\Exception $e) {
            Logger::error('Failed to create group: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id) {
        return view('admin::groups.show', ['id' => encrypt_decrypt('decrypt', $id)]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) {
        $id = encrypt_decrypt('decrypt',$id);
        $group = $this->groupService->firstornew($id); 
        $name = $group->name;
        return view('admin::groups.edit',compact('group','name'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupUpdateService $request, $id) {
        try{
            $data = $request->only('name','status', 'user_ids');
            $group = $this->groupService->firstornew($id);
            $user_ids = explode(",", $data['user_ids']);
            if($data["user_ids"] == null) {
                $user_ids = [];
            }
            $data['user_ids'] = $user_ids;
            $result = $this->groupService->update($group,$data);
            if($result){
                $notification = array(
                'message' => 'You have successfully updated group!',
                'alert-type' => 'success'
                );
                return redirect()->route('groups.list')->with($notification);
            } else {
                return redirect()->back()->withInput();
            }

      } catch (\Exception $ex) {
        Logger::error($request->getHttpHost().": Update site request failed with id=".$id.", Error=".$ex->getMessage());
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        try {
            $id = encrypt_decrypt('decrypt',$id);
            $group = $this->groupService->findFirst($id);
            return $this->groupService->deleteGroup($id);
        } catch(\Exception $ex) {
            Logger::error("Error=".$ex->getMessage());
        }
    }

    public function find(Request $request) {
        $query = $request->input('query');
        if(strlen($query) < 1) return response() -> json(['users' => []]);
        return $this -> groupService -> searchUser($query);
    }

    public function ajaxgetgroups() {
        return $this->groupService->ajaxgetlist();
    }

    public function ajaxgetusers($id) { 
        return $this->groupService->ajaxgetusers($id); 
    }
}
