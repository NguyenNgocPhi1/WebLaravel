<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Models\System;



class ProductController extends FrontendController
{
    protected $productCatalogueRepository;
    protected $productService;
    protected $productRepository;
    protected $productCatalogueService;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductService $productService,
        ProductRepository $productRepository,
        ProductCatalogueService $productCatalogueService,
    ){
        parent::__construct();
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->productCatalogueService = $productCatalogueService;
    }

    public function index($id, $request){
        $language = $this->language;
        $product = $this->productRepository->getProductById($id, $language);
        $product = $this->productService->combineProductAndPromotion([$id], $product, true);
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($product->product_catalogue_id, $language);
        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $language);
        $product = $this->productService->getAttribute($product, $language);
        $category = recursive(
            $this->productCatalogueRepository->all([
                'languages' =>  function($query) use ($language){
                    $query->where('language_id', $language);
                }
            ], categorySelectRaw('product'))
        );
        $system = $this->system;
        $config = $this->config();
        $seo = seo($product);
        return view('frontend.product.product.index', compact(
            'config', 
            'seo',
            'system',
            'productCatalogue',
            'breadcrumb',
            'product',
            'category',
        ));
    }

    private function config(){
        return [
            'language' => $this->language,
            'js' => [
                'frontend/core/library/cart.js'
            ]
        ];
    }
}
