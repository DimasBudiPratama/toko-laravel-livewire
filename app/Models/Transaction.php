<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'invoice_number';
    public $incrementing = false; //Untuk menghandel ID Generator yang sudah di install tadi
    protected $keyType = 'string'; //Maksudnya primary kita ini string
    protected $guarded = [];

    protected $table = 'transaction';

    public function product()
    {
        return $this->hasMany(ProductTransaction::class, 'invoice_number', 'invoice_number');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
