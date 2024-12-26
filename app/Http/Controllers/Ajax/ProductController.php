<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface as ProductVariantRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Models\Language;

class ProductController extends Controller
{
    protected $productRepository;
    protected $productVariantRepository;
    protected $promotionRepository;
    protected $language;
    public function __construct(
        ProductRepository $productRepository,
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
    ){
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function loadProductPromotion(Request $request){
        $get = $request->input();
        $loadClass = loadClass($get['model']);
        if($get['model'] == 'Product'){
            $condition = [
                ['tb2.language_id', '=', $this->language]
            ];
            if(isset($get['keyword']) && $get['keyword'] != ''){
                $keywordCondition = ['tb2.name', 'LIKE', '%'.$get['keyword'].'%'];
                array_push($condition, $keywordCondition);
            }
            $objects = $loadClass->findProductForPromotion($condition);
        }else if($get['model'] == 'ProductCatalogue'){
            $conditionArray['keyword'] = ($get['keyword']) ?? null;
            $conditionArray['where'] = [
                ['tb2.language_id', '=', $this->language]
            ];
            $objects = $loadClass->pagination(
                [
                    'product_catalogues.id',
                    'tb2.name'
                ],
                $conditionArray,
                20,
                ['path' => 'product.catalogue.index'],
                ['product_catalogues.id', 'desc'],
                [
                    ['product_catalogue_language as tb2','tb2.product_catalogue_id','=','product_catalogues.id']
                ]
            );
        }
        return response()->json([
            'model' => ($get['model']) ?? 'Product',
            'objects' => $objects
        ]);    
    }

    public function loadVariant(Request $request){
        $get = $request->input();
        $attributeId = $get['attribute_id'];
        $attributeId = sortAttributeId($attributeId);
        $variant = $this->productVariantRepository->findVariant($attributeId, $get['product_id'], $get['language_id']);
        $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
        $variantPrice = getVariantPrice($variant, $variantPromotion);
        return response()->json([
            'variant' => $variant,
            'variantPrice' => $variantPrice
        ]);
    }

}
