<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class transaksi extends Model
{
    use Notifiable;
    protected $guarded = [];

    public function price()
    {
      return $this->belongsTo(harga::class,'harga_id','id');
    }

    public function prices()
    {
      return $this->belongsToMany(harga::class);
    }

    public function customers()
    {
      return $this->belongsTo(User::class,'customer_id','id')->where('auth','Customer');
    }

    public function user()
    {
      return $this->belongsTo(User::class,'user_id','id');
    }

    public function bank()
    {
      return DataBank::where('nama_bank', $this->payment_method)->first();
    }

}
