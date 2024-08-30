<?php

namespace Modules\Admin\Repositories\Implementations;

use Modules\Admin\Repositories\Interfaces\RecievableInterface;
use Modules\Admin\Entities\Bucket;
use Modules\Admin\Entities\VirtualAccount;
use Modules\Admin\Entities\VirtualTxn;
use Modules\Core\Helpers\Logger;
use Illuminate\Support\Facades\DB;
use Auth;
use Exception;
use Modules\Admin\Entities\SiteUser;

class RecievableRepository implements RecievableInterface
{
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
                        <a href="' . url('admin/recievables/edit', encrypt_decrypt('encrypt', $data->id)) . '">
                            <button type="button" class="btn btn-icon btn-primary">
                                <span class="tf-icons bx bx-pencil bx-22px"></span>
                            </button>
                        </a>
                        <a href="' . url('admin/recievables/show', encrypt_decrypt('encrypt', $data->id)) . '">
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

    public function index()
    {
        $bucketCount = Bucket::where('type', 'payin')->count();
        $totalBalance = VirtualAccount::whereIn('bucket_id', function ($query) {
            $query->select('id')->from('buckets')->where('type', 'payin');
        })->sum('balance');
        return view('admin::recievables.index', compact(['bucketCount', 'totalBalance']));
    }

    public function createBucket($data)
    {
        return DB::transaction(function () use ($data) {
            try {
                $auth_user_id = Auth::user()->id;

                // Create the bucket
                $created_bucket = Bucket::create([
                    'name' => $data['name'],
                    'purpose' => $data['purpose'],
                    'description' => $data['description'],
                    'type' => $data['type']
                ]);

                if (!$created_bucket) {
                    throw new Exception('Error creating a bucket');
                }

                $bucket_id = $created_bucket->id;

                // Retrieve site_id for the authenticated user
                $site_id = SiteUser::where('user_id', $auth_user_id)->value('site_id');

                if ($site_id === null) {
                    throw new Exception('Site ID not found for the user');
                }

                // Create the virtual account
                $created_virtual_account = VirtualAccount::create([
                    'site_id' => $site_id,
                    'balance' => 0.0,
                    'bucket_id' => $bucket_id,
                ]);

                if (!$created_virtual_account) {
                    throw new Exception('Error creating a virtual account');
                }

                $virtual_account_id = $created_virtual_account->id;

                // Generate account number
                $acc_vid = '9509' . $site_id . $bucket_id . '0' . $virtual_account_id;

                // Update the virtual account with the account number
                $created_virtual_account->update([
                    'account_no' => $acc_vid,
                ]);

                return redirect()->route('recievables.list')->with('success', 'Group created successfully.');
            } catch (\Exception $e) {
                Logger::error('Failed to create group: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function edit(string $id)
    {
        $id = encrypt_decrypt('decrypt', $id);
        $bucket = Bucket::where('id', $id)->get();
        $name = $bucket[0]->name;
        $purpose = $bucket[0]->purpose;
        $description = $bucket[0]->description;
        $id = $bucket[0]->id;
        return view('admin::recievables.edit', compact('id', 'name', 'purpose', 'description'));
    }

    public function getBalance(string $id)
    {
        $id = encrypt_decrypt('decrypt', $id);
        $bucket = Bucket::findOrFail($id);
        $balance = VirtualAccount::where('bucket_id', $id)->value('balance');
        $buckets = Bucket::where('id', '!=', $id)->where('type', 'payout')->get();
        return view('admin::recievables.show', compact('bucket', 'buckets', 'balance'));
    }

    public function transferfunds($validated, $id)
    {
        DB::beginTransaction();
        try {
            $sourceBucket = Bucket::findOrFail($id);
            $sourceAccount = VirtualAccount::where('bucket_id', $id)->firstOrFail();
            $totalTransferAmount = array_sum($validated['payoutBuckets']);
            if ($sourceAccount->balance < $totalTransferAmount) {
                return response()->json(['error' => 'Insufficient balance'], 400);
            }
            foreach ($validated['payoutBuckets'] as $bucketId => $amount) {
                $targetBucket = Bucket::find($bucketId);
                $targetAccount = VirtualAccount::where('bucket_id', $bucketId)->first();
                if ($targetBucket && $targetAccount) {
                    VirtualTxn::create([
                        'site_id' => $sourceAccount->site_id,
                        'account_id' => $sourceAccount->account_no,
                        'party_ac_id' => $targetAccount->account_no,
                        'type' => 'debit',
                        'bank_id' => $sourceAccount->bank_id,
                        'particular' => 'Transfer to bucket ' . $targetBucket->id,
                        'utr_no' => null,
                        'amount' => $amount,
                        'balance' => $sourceAccount->balance - $amount,
                        'status' => 1,
                    ]);
                    $sourceAccount->balance -= $amount;
                    $sourceAccount->save();
                    VirtualTxn::create([
                        'site_id' => $targetAccount->site_id,
                        'account_id' => $targetAccount->account_no,
                        'party_ac_id' => $sourceAccount->account_no,
                        'type' => 'credit',
                        'bank_id' => $targetAccount->bank_id,
                        'particular' => 'Transfer from bucket ' . $sourceBucket->id,
                        'utr_no' => null,
                        'amount' => $amount,
                        'balance' => $targetAccount->balance + $amount,
                        'status' => 1,
                    ]);
                    $targetAccount->balance += $amount;
                    $targetAccount->save();
                } else {
                    DB::rollBack();
                    return response()->json(['error' => 'One or more buckets or accounts not found'], 404);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Funds transferred successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Logger::error('Transfer funds failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to transfer funds'], 500);
        }
    }
}
