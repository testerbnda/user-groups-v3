<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Admin\Http\Controllers\UserController;
use Modules\Admin\Http\Controllers\SitesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::group([], function () {
//     Route::resource('admin', AdminController::class)->names('admin');
// });


Route::group(['prefix' => 'admin','name' => 'admin.', 'middleware' => ['auth']], function () {
     
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/ajaxgetusers', [UserController::class, 'ajaxgetusers'])->name('user.ajaxgetusers');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::patch('user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::post('user/destroy', [UserController::class, 'edit'])->name('user.destroy');

    // Sites Routes
    Route::get('sites/list', [SitesController::class, 'index'])->name('sites.list');
    Route::get('sites/create', [SitesController::class, 'create'])->name('sites.create');
    Route::post('sites/store', [SitesController::class, 'store'])->name('sites.store'); 
    Route::get('sites/edit/{id}', [SitesController::class, 'edit'])->name('sites.edit');
    Route::patch('sites/update/{id}', [SitesController::class, 'update'])->name('sites.update');
    Route::get('sites/ajaxgetsites', [SitesController::class, 'ajaxgetsites'])->name('sites.ajaxgetsites');




    // Cache Clear
    Route::get('/clear-cache',function(){
        Artisan::call('cache:clear');
        $notification = array(
         'message' => 'Cache cleared',
         'alert-type' => 'success'
        );
       return redirect()->back()->with($notification);
    })->middleware('role:Superadmin')->name('clear.cache');

    Route::get('/clear-config',function(){
        Artisan::call('config:clear');
        $notification = array(
         'message' => 'Config cleared',
         'alert-type' => 'success'
        );
       return redirect()->back()->with($notification);
    })->middleware('role:Superadmin')->name('clear.config');
 
});
