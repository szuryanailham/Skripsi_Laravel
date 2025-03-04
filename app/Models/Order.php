<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke User (satu order dimiliki oleh satu user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Event (satu order hanya untuk satu event)
    public function event()
    {
        return $this->belongsTo(Events::class);
    }
}
