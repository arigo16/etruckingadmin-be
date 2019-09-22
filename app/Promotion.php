<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model {

    protected $fillable = [        
        'truck_feet_id',
        'truck_type_id',
        'vendor_id',
        'order_type',
        'location_from',
        'location_to',
        'promo_nominal',
        'promo_alias',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [ 

    ];

    public function detailFrom($order_type, $from_id)
    {
        if($order_type == 1 || $order_type == 3)
            return LocDistrict::with('regency', 'regency.province')->where('id', $from_id)->first();

        return Seaport::find($from_id);
    }


    public function detailTo($order_type, $to_id)
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

    public function truck_type()
    {
        return $this->belongsTo('App\TruckType');
    }

    public function truck_feet()
    {
        return $this->belongsTo('App\TruckFeet');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }

    public function checkAvailability($order_type, $location_from, $location_to, $truck_type_id, $truck_feet_id)
    {
        return self::where('order_type', $order_type)->where('location_from', $location_from)->where('location_to', $location_to)->where('truck_type_id', $truck_type_id)->where('truck_feet_id', $truck_feet_id)->count();
    }
}