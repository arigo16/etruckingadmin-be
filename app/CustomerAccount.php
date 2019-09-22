<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAccount extends Model {

    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'bank_name',
        'account_name',
        'account_number',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [ 

    ];

    protected $dates = ['deleted_at'];

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }
}