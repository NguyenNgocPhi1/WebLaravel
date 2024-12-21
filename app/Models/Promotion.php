<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Promotion extends Model
{
    use HasFactory, QueryScopes, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'method',
        'discountInformation',
        'neverEndDate',
        'startDate',
        'endDate',
        'publish',
        'order',
        'discountValue',
        'discountType',
        'maxDiscountValue',
    ];

    protected $casts = [
        'discountInformation' => 'json'
    ];

    protected $table = 'promotions';

    public function products(){
        return $this->belongsToMany(Promotion::class,'promotion_product_variant','promotion_id','product_id')
        ->withPivot('variant_uuid','model')->withTimestamps();
    }
}
