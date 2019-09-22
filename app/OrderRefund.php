<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderRefund extends Model {

    use SoftDeletes;

    protected $fillable = [
        'account_name',
        'account_number',
        'bank_name',
        'amount',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [ 

    ];

    protected $dates = ['deleted_at'];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function driver()
    {
        return $this->belongsTo('App\Driver');
    }
}