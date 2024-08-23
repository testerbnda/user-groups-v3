<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role;
use Modules\Paymentgatyway\Entities\Paymentgatyway;
use Modules\Apidoc\Entities\Apidoc;
use Modules\User\Entities\Sitegateway;
use Auth;
use DB;

class Sites extends Model
{
    use HasFactory;

    protected $fillable = ['site_name','site_code','status'];

    protected static function newFactory()
    {
        return \Modules\User\Database\factories\SitesFactory::new();
    }

     public function associateroles()
    {
        $user     = \Auth::user();
        $userRole = $user->roles->first();
        if ($userRole->id == 1) {
        return $this->hasMany(Role::class,'site_id')->select(['site_id','id','name'])->orderBy('name');
        } else {
            $siteids = Auth::user()->roles->pluck('id')->toArray();
         $roles = DB::table('roles')->select('roles.id', 'roles.name', 'sites.site_name')->leftJoin('sites', 'roles.site_id', 'sites.id')->whereIn('roles.id', function ($query) use ($siteids) {
                $query->select('role_id')->from('role_manager')->whereIn('parent_id', $siteids);
            })->pluck('id')->toArray();
        return $this->hasMany(Role::class,'site_id')->whereIn('roles.id',$roles)->select(['site_id','id','name'])->orderBy('name');
        }
    }


    // public function associateSiteGateway()
    // {
    //     return $this->belongsToMany(Paymentgatyway::class,'site_gateways');
    // }

    // public function associateapis()
    // {
    //     return $this->belongsToMany(Apidoc::class,'api_master_sites');
    // }


}
