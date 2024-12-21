<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Http\Requests\Product\StoreProductCatalogueRequest;
use App\Http\Requests\Product\UpdateProductCatalogueRequest;
// use App\Http\Requests\DeleteProductCatalogueRequest;
use App\Classes\Nestedsetbie;
use App\Models\Language;


class ProductCatalogueController extends Controller
{
    protected $productCatalogueService;
    protected $productCatalogueRepository;
    protected $language;
    
    public function __construct(ProductCatalogueService $productCatalogueService,ProductCatalogueRepository $productCatalogueRepository){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->productCatalogueService = $productCatalogueService;
        $this->productCatalogueRepository = $productCatalogueRepository;
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' => $this->language
        ]);
    }
        
    
    public function index(Request $request){
        $this->authorize('modules', 'product.catalogue.index');
        $productCatalogues = $this->productCatalogueService->paginate($request,$this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'ProductCatalogue'
        ];
        $config['seo'] = __('messages.productCatalogue');
        $template = 'backend.product.catalogue.index';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'productCatalogues'
        ));
    }
    public function create(){
        $this->authorize('modules', 'product.catalogue.create');
        $config = $this->configData();
        $config['seo'] = __('messages.productCatalogue');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.product.catalogue.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StoreProductCatalogueRequest $request){
        if($this->productCatalogueService->create($request, $this->language)){
            return redirect()->route('product.catalogue.index')->with('success','Thêm mới bản ghi thành công');    
        }
        return redirect()->route('product.catalogue.index')->with('error','Thêm mới bản ghi thất bại. Hãy thử lại');    
    }

    public function edit($id){
        $this->authorize('modules', 'product.catalogue.update');
        $productCatalogues = $this->productCatalogueRepository->getProductCatalogueById($id,$this->language);
        $config = $this->configData();
        $config['seo'] = __('messages.productCatalogue');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.product.catalogue.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'productCatalogues',
            'dropdown',
        ));
    }

    public function update($id,UpdateProductCatalogueRequest $request){
        if($this->productCatalogueService->update($id,$request,$this->language)){
            return redirect()->route('product.catalogue.index')->with('success','Cập nhật bản ghi thành công');    
        }
        return redirect()->route('product.catalogue.index')->with('error','Cập nhật bản ghi thất bại. Hãy thử lại');    
    }

    public function delete($id){
        $this->authorize('modules', 'product.catalogue.destroy');
        $productCatalogues = $this->productCatalogueRepository->getProductCatalogueById($id,$this->language);
        $config['seo'] = __('messages.productCatalogue');
        $template = 'backend.product.catalogue.delete';
        return view('backend.dashboard.layout',compact(
            'template',
            'productCatalogues',
            'config'
        ));
    }

    public function destroy($id){
        if($this->productCatalogueService->destroy($id, $this->language)){
            return redirect()->route('product.catalogue.index')->with('success','Xóa bản ghi thành công');    
        }
        return redirect()->route('product.catalogue.index')->with('error','Xóa bản ghi thất bại. Hãy thử lại');  
    }

    private function configData(){
        return [
            'js' => [
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
    } 
    
}
