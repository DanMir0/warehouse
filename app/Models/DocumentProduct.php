<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentProduct extends Model
{
//    use HasFactory;
    public $timestamps = false;
    protected $table = 'documents_products';
    protected $fillable = ['document_id', 'product_id', 'quantity'];
}
