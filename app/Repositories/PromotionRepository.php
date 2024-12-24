<?php

namespace App\Repositories;
use App\Models\Promotion;
use App\Repositories\Interfaces\PromotionRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class PromotionRepository extends BaseRepository implements PromotionRepositoryInterface
{
    protected $model;

    public function __construct(Promotion $model){
        $this->model = $model;
    }
    
    public function findPromotionByProduct(array $productId = []){
        // dd($productId);
        return $this->model->select(
            'promotions.id as promotion_id',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
            'products.id as product_id',
            'products.price as product_price',
        )
        ->selectRaw(
            "
                MAX(
                        IF(promotions.maxDiscountValue != 0, 
                        LEAST(
                            CASE
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN (products.price * discountValue / 100)
                                ELSE 0
                            END,
                            promotions.maxDiscountValue
                        ),
                        CASE
                            WHEN discountType = 'cash' THEN discountValue
                            WHEN discountType = 'percent' THEN (products.price * discountValue / 100)
                            ELSE 0
                        END
                    )
                ) as discount
            "
        )
        ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
        ->join('products', 'products.id', '=', 'ppv.product_id')
        ->where('products.publish', 2)
        ->where('promotions.publish', 2)
        ->whereIn('products.id', $productId)
        ->whereDate('promotions.endDate', '>', now())
        ->groupBy(['products.id', 'promotions.discountValue', 'promotions.discountType', 'promotions.id', 'promotions.maxDiscountValue', 'products.price'])
        ->get();
    }

    public function findPromotionByVariantUuid($uuid){
        return $this->model->select(
            'promotions.id as promotion_id',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
        )
        ->selectRaw(
            "
                MAX(
                        IF(promotions.maxDiscountValue != 0, 
                        LEAST(
                            CASE
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN (products.price * discountValue / 100)
                                ELSE 0
                            END,
                            promotions.maxDiscountValue
                        ),
                        CASE
                            WHEN discountType = 'cash' THEN discountValue
                            WHEN discountType = 'percent' THEN (products.price * discountValue / 100)
                            ELSE 0
                        END
                    )
                ) as discount
            "
        )
        ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
        ->join('products', 'products.id', '=', 'ppv.product_id')
        ->where('products.publish', 2)
        ->where('promotions.publish', 2)
        ->whereIn('products.id', $productId)
        ->whereDate('promotions.endDate', '>', now())
        ->groupBy(['products.id', 'promotions.discountValue', 'promotions.discountType', 'promotions.id', 'promotions.maxDiscountValue', 'products.price'])
        ->get();
    }
    
}
