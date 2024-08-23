<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Userbank extends Model
{
    /**
     * @var string[]
     */
    const UPDATED_AT = null;
    protected $fillable = ['user_id', 'name', 'bank_name', 'bank_account', 'bank_ifsc','verified','bank_response_data','created_at'];
    protected $hidden = [
        'created_at','updated_at','bank_status'
    ];

}
