<?php
  
namespace App\Imports;
  
use App\OrderRefund;
use Maatwebsite\Excel\Concerns\ToModel;
  
class OrderRefundImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new OrderRefund([
            'id'                => $row[0],
            'account_name'      => $row[1],
            'account_number'    => $row[2], 
            'bank_name'         => $row[3],
            'amount'            => $row[4],
            'status'            => $row[5],
            'created_at'        => $row[6]
        ]);
    }
}