<?php

namespace Modules\Admin\Repositories\Implementations;
use Modules\Admin\Repositories\Interfaces\RecievableInterface;
use Modules\Admin\Entities\Bucket;
use Modules\Admin\Entities\VirtualAccount;
use Modules\Core\Helpers\Logger;
class RecievableRepository implements RecievableInterface {
    //

    public function ajaxgetrecievables()
{
    if (request()->ajax()) {
        $data = Bucket::with('virtualAccount')
            ->select('buckets.*')
            ->where('type', 'payin')
            ->orderBy('buckets.created_at', 'desc')
            ->get();

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
                $va = $request->virtualAccount;
                return $va ? $va->account_no : 'No Account';
            })
            ->editColumn('balance', function ($request) {
                $va = $request->virtualAccount;
                return $va ? $va->balance : 'No Balance';
            })
            ->addColumn('action', function ($data) {
                return '<div class="demo-inline-spacing">
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
        $bucketCount = Bucket::where('type', 'payin')->count();
        $totalBalance = VirtualAccount::whereIn('bucket_id', function($query) {
            $query->select('id')->from('buckets')->where('type', 'payin');
        })->sum('balance');
        return view('admin::recievables.index', compact(['bucketCount', 'totalBalance']));
    }
}