<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['document_type_id', 'counterparty_id', 'order_id'];

    public function counterparty()
    {
        return $this->belongsTo(Counterparties::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function documentProducts()
    {
        return $this->hasMany(DocumentProduct::class); // или другая связь в зависимости от вашего случая
    }
}
