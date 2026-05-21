<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['productName', 'amountAvailable', 'cost', 'sellerId'];

    protected function casts(): array
    {
        return [
            'amountAvailable' => 'integer',
            'cost'            => 'integer',
            'sellerId'        => 'integer',
        ];
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'sellerId');
    }
}
