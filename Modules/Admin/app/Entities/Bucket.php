<?php

namespace Modules\Admin\Entities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bucket extends Model
{
    use HasFactory;
    // Table name
    protected $table = 'bucket';

    // Primary Key
    protected $primaryKey = 'id';

    // Auto-incrementing primary key
    public $incrementing = true;

    // Data type for the primary key
    protected $keyType = 'unsignedBigInteger';

    // Timestamps
    public $timestamps = true;

    // Fillable fields for mass assignment
    protected $fillable = [
        'name',
        'vid_ac',
        'balance',
    ];

    // The attributes that should be cast to native types
    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // Disable timestamps if necessary
    // public $timestamps = false;
}
