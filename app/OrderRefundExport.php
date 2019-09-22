<?php
  
namespace App\Exports;
  
use App\OrderRefund;
use Maatwebsite\Excel\Concerns\FromCollection;
  
class OrderRefundExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return OrderRefund::all();
    }
}