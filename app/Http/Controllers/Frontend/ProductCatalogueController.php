<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Models\System;



class ProductCatalogueController extends FrontendController
{
    protected $productCatalogueRepository;
    protected $productService;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductService $productService,
    ){
        parent::__construct();
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productService = $productService;
    }

    public function index($id, $request, $page = 1){
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);
        $products = $this->productService->paginate(
            $request, 
            $this->language,
            $productCatalogue, 
            $page,
            ['path' => $productCatalogue->canonical],
        );
        $productId = $products->pluck('id')->toArray();
        if(count($productId) && !is_null($productId)){
            $products = $this->productService->combineProductAndPromotion($productId, $products);
        }
        $system = $this->system;
        $config = $this->config();
        $seo = seo($productCatalogue, $page);
        return view('frontend.product.catalogue.index', compact(
            'config', 
            'seo',
            'system',
            'productCatalogue',
            'breadcrumb',
            'products',
        ));
    }

    private function config(){
        return [
            'language' => $this->language
        ];
    }
}
