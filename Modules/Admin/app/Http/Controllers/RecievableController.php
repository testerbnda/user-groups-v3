<?php

namespace Modules\Admin\Http\Controllers;
use Modules\Admin\Services\RecievableService;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

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
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
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
}