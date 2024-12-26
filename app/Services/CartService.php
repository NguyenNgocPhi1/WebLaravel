<?php

namespace App\Services;

use App\Services\Interfaces\CartServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface as ProductVariantRepository;
use Cart;
/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class CartService implements CartServiceInterface
{
    protected $productRepository;
    protected $productVariantRepository;
    protected $productService;
    protected $promotionRepository;
    public function __construct(
        ProductRepository $productRepository,
        ProductVariantRepository $productVariantRepository,
        ProductService $productService,
        PromotionRepository $promotionRepository,
    ){
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->productService = $productService;
        $this->promotionRepository = $promotionRepository;
    }
    
    public function create($request, $language = 1){
        try{
            $payload = $request->input();
            $product = $this->productRepository->findById($payload['id'], ['*'], ['languages' => function($query) use ($language){
                $query->where('language_id', $language);
            }]);
            $data = [
                'id' => $product->id,
                'name' => $product->languages->first()->pivot->name,
                'qty' => $payload['quantity'],

            ];
            if(!empty($payload['attribute_id']) && count($payload['attribute_id'])){
                $attributeId = sortAttributeId($payload['attribute_id']);
                $variant = $this->productVariantRepository->findVariant($attributeId, $product->id, $language);
                $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
                $variantPrice = getVariantPrice($variant, $variantPromotion);
                $data['id'] = $product->id.'_'.$variant->uuid;
                $data['name'] = $product->languages->first()->pivot->name.' '.$variant->languages->first()->pivot->name;
                $data['price'] = ($variantPrice['priceSale'] > 0) ? $variantPrice['priceSale'] : $variantPrice['price'];
                $data['options'] = [
                    'attribute' => $payload['attribute_id'],
                ];
            }else{
                $product = $this->productService->combineProductAndPromotion([$product->id], $product, true);
                $price = getPrice($product);
                $data['price'] = ($price['priceSale'] > 0) ? $price['priceSale'] : $price['price'];
            }
            Cart::instance('shopping')->add($data);
            return true;
        }catch(\Exception $e ){
            echo $e->getMessage();die();
            return false;
        }
    }
    
}
