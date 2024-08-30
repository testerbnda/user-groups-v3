<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Entities\Sites;

class VirtualTxn extends Model
{
    use HasFactory;

    protected $table = 'virtual_txns'; 

    protected $primaryKey = 'id'; 

    public $incrementing = true; 

    protected $fillable = [
        'site_id',
        'account_id',
        'party_ac_id',
        'type',
        'bank_id',
        'particular',
        'utr_no',
        'amount',
        'balance',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define relationships if any
    public function site()
    {
        return $this->belongsTo(Sites::class, 'site_id');
    }

    // Add other relationships or methods as needed
}
