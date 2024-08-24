<?php
namespace Modules\Admin\Repositories\Implementations;
use Modules\Core\Helpers\Logger;
use Modules\Admin\Repositories\Interfaces\GroupInterface;
use Modules\Admin\Entities\Group;
use App\Models\User;
use DB;


class GroupRepository implements GroupInterface {
    public function createGroup($data) {
        return DB::transaction(function () use ($data) {
            try {
                $group = Group::create([
                    'name' => $data['name'],
                    'status' => $data['status'],
                    'site_id' => $data['site_id'],
                ]);

                if (!empty($data['user_ids'])) {
                    $group->users()->sync($data['user_ids']);
                }

                Logger::info('Group created successfully.', ['group_id' => $group->id]);
                return redirect()->route('groups.list')->with('success', 'Group Created successfully.');
            } catch (\Exception $e) {
                Logger::error('Failed to create group: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function search($query) {
        try {
            $users = User::where('name', 'like', '%' . $query . '%')->limit(10) -> get(['id', 'name', 'email']); 
            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            Logger::error('Failed to create group: ' . $e->getMessage());
            throw $e;
        }
    }

    public function ajaxgetlist() {
        if (request()->ajax()) { 
            $data = Group::select('groups.*') 
                ->orderBy('groups.status', 'desc')
                ->orderBy('groups.created_at', 'desc')->get();
            return datatables()->of($data)
                ->setRowClass(function ($request) {
                    return $request->status == 1 ? 'nk-tb-item' : 'nk-tb-item font-italic text-muted';
                })
                ->editColumn('created_at', function ($request) {
                    return $request->created_at->format('d/m/Y H:i:s');
                    // return [
                    //     'display'   => $request->created_at->format('d/m/Y'),
                    //     'timestamp' => $request->created_at,
                    // ];
                })
                ->editColumn('name', function ($request) {
                   return $request->name;
                })
            
                ->editColumn('status', function ($request) {
                    if ($request->status == 1) {
                        return '<label class="custom-control-label" for="customSwitch' . $request->id . '">Active</label>';
                   } else {
                       return '<label class="custom-control-label" for="customSwitch' . $request->id . '">Inactive</label>';
                   }
                })
                 
                // Create group edit code
                ->addColumn('action', function ($data) {
                    // return '<a class="btn btn-success btn-sm" href="'.url('admin/sites/edit',$data->id).'">Edit</a>';
                    return $link =  '<div class="demo-inline-spacing">
                    <a href="' . url('admin/groups/edit', encrypt_decrypt('encrypt',$data->id)) . '" ><button type="button" class="btn btn-icon btn-primary">
                    <span class="tf-icons bx bx-pencil bx-22px"></span>
                    </button></a>
                </div>';
                })
                ->rawColumns(['created_at', 'name', 'status' ,'action'])
                ->make(true);
        }
    }

    public function firstornew($id) {
        if (!empty($id)) {

            return Group::where('id', $id)->first();
        }
        return new Group();
    }

    public function update(Group $group, array $data) {
        return DB::transaction(function () use ($group, $data) {
            try {
                $updated_group = $group -> update($data);
                if($updated_group) {
                    Logger::info('Group updated successfully.', ['group_id' => $group->id]);
                }
                // if (!empty($data['user_ids'])) {
                //     $group->users()->sync($data['user_ids']);
                // }

                return redirect()->route('groups.list')->with('success', 'Group Updated successfully.');
            } catch (\Exception $e) {
                Logger::error('Failed to create group: ' . $e->getMessage());
                throw $e;
            }
        });
    } 
}
