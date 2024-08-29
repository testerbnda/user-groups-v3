<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class VirtualAccount extends Model
{
    // Table name
    protected $table = 'virtual_accounts';

    // Primary Key
    protected $primaryKey = 'id';

    // Disable auto-incrementing since it's unsigned
    public $incrementing = true;

    // Timestamps
    public $timestamps = true;

    // Fillable fields
    protected $fillable = [
        'site_id',
        'account_no',
        'ac_name',
        'ac_type',
        'balance',
        'bank_name',
        'ifsc_code',
        'status',
        'nick_name',
        'bucket_id', // Add this if you need to include the foreign key
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'balance' => 'decimal:2',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define the relationship with the Bucket model
    public function bucket()
    {
        return $this->belongsTo(Bucket::class, 'bucket_id');
    }
}
