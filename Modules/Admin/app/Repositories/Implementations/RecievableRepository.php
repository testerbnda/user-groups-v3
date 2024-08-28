<?php

namespace Modules\Admin\Repositories\Implementations;
use Modules\Admin\Repositories\Interfaces\RecievableInterface;
use Modules\Admin\Entities\Bucket;
use Modules\Core\Helpers\Logger;
class RecievableRepository implements RecievableInterface {
    //

    public function ajaxgetrecievables() {   
        if (request()->ajax()) {
            $data = Bucket::select('bucket.*')
                ->orderBy('bucket.created_at', 'desc')->get();
            $totalBalance = $data->sum('balance');
            $totalBuckets = $data->count();
            return datatables()->of($data)
                ->setRowClass(function ($request) {
                    return 'nk-tb-item';
                })
                ->editColumn('created_at', function ($request) {
                    return $request->created_at->format('d/m/Y H:i:s');
                })
                ->editColumn('name', function ($request) {
                    return $request->name;
                })
                ->editColumn('vid_ac', function ($request) {
                    return $request->vid_ac;
                })
                ->editColumn('balance', function ($request) {
                    return $request->balance;
                })
                // Create group edit code
                ->addColumn('action', function ($data) {
                    // return '<a class="btn btn-success btn-sm" href="'.url('admin/sites/edit',$data->id).'">Edit</a>';
                    return $link = '<div class="demo-inline-spacing">
                            <a href="' . url('#', encrypt_decrypt('encrypt', $data->id)) . '">
                            <button type="button" class="btn btn-icon btn-primary">
                                <span class="tf-icons bx bx-pencil bx-22px"></span>
                            </button>
                        </a>
                        
                        <a href="' . url('#', encrypt_decrypt('encrypt', $data->id)) . '">
                            <button type="button" class="btn btn-icon btn-primary">
                                <span class="tf-icons bx bx-show bx-22px"></span>
                            </button>
                        </a>
                    </div>';
                })

                ->rawColumns(['created_at', 'name', 'vid_ac', 'balance', 'action'])
                ->make(true);
                
        }
    }

    public function index() {
        $bucketCount = Bucket::count();
        $totalBalance = Bucket::sum('balance');
        return view('admin::recievables.index', compact(['bucketCount', 'totalBalance']));
    }
}