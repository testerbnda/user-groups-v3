<?php

namespace Modules\Admin\Http\Controllers;
use Modules\Admin\Services\RecievableService;
use App\Http\Controllers\Controller;
use Modules\Admin\Http\Requests\RecievableUpdateService;
use Illuminate\Http\Request;
use Modules\Core\Helpers\Logger;    
use Auth;
class RecievableController extends Controller
{
    private $recievableService;

    // Constructor
    public function __construct(RecievableService $recievableService)
    {
        $this->recievableService = $recievableService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this -> recievableService -> index();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return view('admin::recievables.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:45',
                'purpose' => 'required|string|max:1000',
                'description' => 'required|string',  // Adjust if you need to limit the size of the description
            ]);
            $data['type'] = 'payin';
            $createdBucket = $this -> recievableService -> createBucket($data);
            if($createdBucket) {
                $notification = array(
                    'message' => 'You have successfully created a bucket!',
                    'alert-type' => 'success'
                );
            } else {
                $notification = array(
                    'message' => 'Can\'t create bucket, please try again!',
                    'alert-type' => 'error'
                );
            }
            return redirect()->route('recievables.list') -> with($notification);
        } catch(\Exception $ex) {
            Logger::error($ex);
            throw $ex;
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return $this -> recievableService -> getBalance($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        return $this -> recievableService -> edit($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecievableUpdateService $request, $id)
    {
        Logger::info(json_encode(["ID" => $id, "data" => $request]));
        return $id;   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function ajaxgetrecievables()
    {
        return $this->recievableService->ajaxgetrecievables();
    }

    public function transferfunds(Request $request, $id) {
        $validated = $request->validate([
            'payoutBuckets' => 'required|array',
            'payoutBuckets.*' => 'required|numeric|min:1',
        ]);
        return $this -> recievableService -> transferfunds($validated, $id);
    }

    public function ajaxgettransactions($id) {
        return $this -> recievableService -> ajaxgettransactions($id);
    }
}
