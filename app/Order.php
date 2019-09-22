<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $fillable = [];

    //order type
    //1 = export
    //2 = import
    
    protected $hidden = [ 

    ];

    public function order_detail()
    {
        return $this->hasOne('App\OrderDetail');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function location_pickup($order_type, $from_id)
    {
        if($order_type == 1 || $order_type == 3)
            return LocDistrict::with('regency', 'regency.province')->where('id', $from_id)->first();

        return Seaport::find($from_id);
    }

    public function location_delivery($order_type, $to_id)
    {
        if($order_type == 2 || $order_type == 3)
            return LocDistrict::with('regency', 'regency.province')->where('id', $to_id)->first();
        
        return Seaport::find($to_id);
    }

    public function orderTypeAlias($order_type)
    {
        if($order_type == 1)
            return "Export";
        else if($order_type == 2)
            return "Import";
        else
            return "Domestic";
    }
}