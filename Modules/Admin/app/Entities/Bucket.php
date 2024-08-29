<?php
namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Bucket extends Model
{
    // Table name
    protected $table = 'buckets';

    // Primary Key
    protected $primaryKey = 'id';

    // Disable auto-incrementing since it's unsigned
    public $incrementing = true;

    // Timestamps
    public $timestamps = true;

    // Fillable fields
    protected $fillable = [
        'name',
        'purpose',
        'description',
        'type',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define the one-to-one relationship with VirtualAccount
    public function virtualAccount()
    {
        return $this->hasOne(VirtualAccount::class, 'bucket_id');
    }
}
