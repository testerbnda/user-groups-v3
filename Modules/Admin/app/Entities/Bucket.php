<?php

namespace Modules\Admin\Entities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bucket extends Model
{
    use HasFactory;
    // Table name
    protected $table = 'bucket';

    // Auto-incrementing primary key
    public $incrementing = true;

    // Timestamps
    public $timestamps = true;

     /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'vid_ac',
        'balance',
        'created_at',
        'updated_at',
    ];

    // The attributes that should be cast to native types
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Disable timestamps if necessary
    // public $timestamps = false;
}
