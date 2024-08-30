<?php

namespace Modules\Admin\Entities;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteUser extends Model
{
    use HasFactory;

    protected $table = 'site_users';

    protected $fillable = [
        'site_id',
        'user_id',
        'role',
        'bank_id',
        'status',
    ];

    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'role' => 'string',
        'bank_id' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the site that owns the SiteUser.
     */
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    /**
     * Get the user that owns the SiteUser.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
