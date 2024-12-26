<?php

namespace App\Services;

use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use App\Repositories\Interfaces\ProductVariantLanguageRepositoryInterface as ProductVariantLanguageRepository;
use App\Repositories\Interfaces\ProductVariantAttributeRepositoryInterface as ProductVariantAttributeRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogueRepository;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid; //Thư viện uuid cài bằng composer require ramsey/uuid
use Illuminate\Pagination\Paginator;
/**
 * Class ProductService
 * @package App\Services
 */
class ProductService extends BaseService implements ProductServiceInterface
{
    protected $productRepository;
    protected $routerRepository;
    protected $productVariantLanguageRepository;
    protected $productVariantAttributeRepository;
    protected $promotionRepository;
    protected $attributeCatalogueRepository;
    protected $attributeRepository;
    
    public function __construct(
        ProductRepository $productRepository, 
        RouterRepository $routerRepository, 
        ProductVariantLanguageRepository $productVariantLanguageRepository, 
        ProductVariantAttributeRepository $productVariantAttributeRepository,
        PromotionRepository $promotionRepository,
        AttributeCatalogueRepository $attributeCatalogueRepository,
        AttributeRepository $attributeRepository,
    ){
        $this->productRepository = $productRepository;
        $this->routerRepository = $routerRepository;
        $this->productVariantLanguageRepository = $productVariantLanguageRepository;
        $this->productVariantAttributeRepository = $productVariantAttributeRepository;
        $this->promotionRepository = $promotionRepository;
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this->attributeRepository = $attributeRepository;
        $this->controllerName = 'ProductController';
    }
    public function paginate($request, $languageId, $productCatalogue = null, $page = 1, $extend = []){
        if(!is_null($productCatalogue)){
            Paginator::currentPageResolver(function () use ($page){
                return $page;
            });
        }
        
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ]; 
        // $perpage = $request->integer('perpage'); 
        $perpage = (!is_null($productCatalogue)) ? 15 : 20;
        $extend = [
            'path' => ($extend['path']) ?? 'product/index', 
            'groupBy' => $this->paginateSelect()
        ]; 
        $orderBy = [
            'products.id', 'desc'
        ];
        $join = [
            ['product_language as tb2','tb2.product_id','=','products.id'],
            ['product_catalogue_product as tb3','products.id','=','tb3.product_id'],
        ]; 
        $relations = ['product_catalogues'];
        $rawQuery = $this->whereRaw($request, $languageId, $productCatalogue);

        $product = $this->productRepository->pagination(
            $column,
            $condition, 
            $perpage, 
            $extend, 
            $orderBy,
            $join,
            $relations,
            $rawQuery
        );
        if(!is_null($productCatalogue)){
            $product->setPageName('trang');
        }
        return $product;
    }
    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $product = $this->createForProduct($request);
            if($product->id > 0){
                $this->updateLanguageForProduct($product,$request, $languageId);
                $this->updateCatalogueForProduct($product,$request);
                $this->createRouter($product->id,$request, $this->controllerName, $languageId);
                if($request->input('attribute')){
                    $this->createVariant($product, $request, $languageId);
                }
            }     
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }
    public function update($id,$request, $languageId){
        DB::beginTransaction();
        try{
            $product = $this->productRepository->findById($id);
            if($this->uploadProduct($product, $request)){
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForProduct($product, $request);
                $this->updateRouter($product->id,$request, $this->controllerName, $languageId);
                $product->product_variants()->each(function($variant){
                    $variant->languages()->detach();                    
                    $variant->attributes()->detach();                    
                    $variant->delete();                    
                });
                if($request->input('attribute')){
                    $this->createVariant($product, $request, $languageId);
                }
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $product = $this->productRepository->delete($id); //soft delete
            // $this->routerRepository->forceDeleteByCondition(
            //     ['module_id', '=', $id],
            //     ['language_id', '=', $languageId],
            //     ['controllers', '=', 'App\Http\Controllers\Frontend\ProductController']
            // );  
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            Log::error($e->getMessage());
            // echo $e->getMessage();die();
            return false;
        }
    }

    private function createVariant($product, $request, $languageId){
        $payload = $request->only(['variant', 'productVariant', 'attribute']);
        $variant = $this->createVariantArray($payload, $product);
        $variants = $product->product_variants()->createMany($variant);
        $variantsId = $variants->pluck('id');
        $productVariantLanguage = [];
        $variantAttribute = [];
        $attributeCombines = $this->comebineAttribute(array_values($payload['attribute']));
        if(count($variantsId)){
            foreach($variantsId as $key => $val){
                $productVariantLanguage[] = [
                    'product_variant_id' => $val,
                    'language_id' => $languageId,
                    'name' => $payload['productVariant']['name'][$key]
                ];
                if(count($attributeCombines)){
                    foreach($attributeCombines[$key] as $attributeId){
                        $variantAttribute[] = [
                            'product_variant_id' => $val,
                            'attribute_id' => $attributeId
                        ];
                    }
                }
            }
        }
        $variantLanguage = $this->productVariantLanguageRepository->createBatch($productVariantLanguage);
        $variantAttribute = $this->productVariantAttributeRepository->createBatch($variantAttribute);
    }


    private function comebineAttribute($attributes = [], $index = 0){
        if($index === count($attributes)) return [[]];
        $subCombines = $this->comebineAttribute($attributes, $index + 1);
        $combines = [];
        foreach($attributes[$index] as $key => $val){
            foreach($subCombines as $keySub => $valSub){
                $combines[] = array_merge([$val], $valSub);
            }
        } 
        return $combines;
    }

    

    private function createVariantArray(array $payload = [], $product): array{
        $variant = [];
        if(isset($payload['variant']['sku']) && count($payload['variant']['sku'])){
            foreach($payload['variant']['sku'] as $key => $val){
                $vId = ($payload['productVariant']['id'][$key]) ?? '';
                $productVariantId = sortString($vId);
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $product->id.', '.$payload['productVariant']['id'][$key]);
                $variant[] = [
                    'uuid' => $uuid,
                    'code' => $productVariantId,
                    'quantity' => ($payload['variant']['quantity'][$key]) ?? 0,
                    'sku' => $val,
                    'price' => ($payload['variant']['price'][$key]) ? convert_price($payload['variant']['price'][$key]) : 0,
                    'barcode' => ($payload['variant']['barcode'][$key]) ?? '',
                    'file_name' => ($payload['variant']['file_name'][$key]) ?? '',
                    'file_url' => ($payload['variant']['file_url'][$key]) ?? '',
                    'album' => ($payload['variant']['album'][$key]) ?? '',
                    'user_id' => Auth::id(),
                ];
            }
        }
        return $variant;
    }

    private function createForProduct($request){
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $payload['price'] = convert_price($payload['price'] ?? 0);
        $payload['attributeCatalogue'] = $this->formatJson($request, 'attributeCatalogue');
        $payload['attribute'] = $request->input('attribute');
        $payload['variant'] = $this->formatJson($request, 'variant');
        $product = $this->productRepository->create($payload);
        return $product;
    }

    private function uploadProduct($product, $request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['price'] = convert_price($payload['price'] ?? 0);
        return $this->productRepository->update($product->id,$payload);
    }

    private function updateLanguageForProduct($product, $request, $languageId){
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $product->id, $languageId);
        $product->languages()->detach([$languageId, $product->id]);
        return $this->productRepository->createPivot($product, $payload,'languages');
    }

    private function updateCatalogueForProduct($product, $request){
        return $product->product_catalogues()->sync($this->catalogue($request));
    }

    private function formatLanguagePayload($payload, $productId, $languageId){
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['product_id'] = $productId;
        return $payload;
    }

    private function catalogue($request){
        if($request->input('catalogue') != null){
            return array_unique(array_merge($request->input('catalogue'),[$request->product_catalogue_id]));
        }
        return [$request->product_catalogue_id];
    }

    private function paginateSelect(){
        return [
            'products.id', 'tb2.name', 'tb2.canonical', 'products.publish', 'products.image','products.order','products.follow', 'products.price'
        ];
    }

    private function whereRaw($request, $languageId, $productCatalogue = null){
        $rawCondition = [];
        if($request->integer('product_catalogue_id') > 0 || !is_null($productCatalogue)){
            $catId = ($request->integer('product_catalogue_id') > 0) ? $request->integer('product_catalogue_id') : $productCatalogue->id;
            $rawCondition['whereRaw'] = [
                [
                    'tb3.product_catalogue_id IN (
                        SELECT id FROM product_catalogues
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                    )',
                    [$catId, $catId]
                ]
            ];
        }
        return $rawCondition;
    }

    private function payload(){
        return ['follow','publish','image','album','product_catalogue_id', 'price', 'made_in', 'code', 'attributeCatalogue','attribute', 'variant'];
    }
    private function payloadLanguage(){
        return ['name','description','content','meta_title','meta_keyword','meta_description','canonical'];
    }



    public function combineProductAndPromotion($productId = [], $products, $flag = false){
        $promotions = $this->promotionRepository->findPromotionByProduct($productId);
        if($promotions){
            if($flag == true){
                $products->promotions = ($promotions[0]) ?? [];
                return $products;
            }
            foreach ($products as $index => $product) {
                foreach($promotions as $key => $promotion){
                    if($promotion->product_id == $product->id){
                        $products[$index]->promotions = $promotion;
                    }
                }
            }
        }
        return $products;
    }

    public function getAttribute($product, $language){
        $product->attributeCatalogue = [];
        if(isset($product->attribute) && !is_null($product->attribute)){
            $attributeCatalogueId = array_keys($product->attribute);
            $attrCatalogues = $this->attributeCatalogueRepository->getAttributeCatalogueWhereIn($attributeCatalogueId, 'attribute_catalogues.id', $language);
            /* ---- */
            $attributeId = array_merge(...$product->attribute);
            $attrs = $this->attributeRepository->findAttributeByIdArray($attributeId, $language);
            if(!is_null($attrCatalogues)){
                foreach($attrCatalogues as $key => $val){
                    $tempAttributes = [];
                    foreach($attrs as $attr){
                        if($val->id == $attr->attribute_catalogue_id){
                            $tempAttributes[] = $attr;
                        }
                    }
                    $val->attributes = $tempAttributes;
                }
            }
            $product->attributeCatalogue = $attrCatalogues;
        }
        return $product;
    }

}
