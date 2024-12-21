<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogue;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
// use App\Http\Requests\DeleteProductRequest;
use App\Classes\Nestedsetbie;
use App\Models\Language;


class ProductController extends Controller
{
    protected $productService;
    protected $productRepository;
    protected $language;
    protected $attributeCatalogue;
    
    public function __construct(ProductService $productService,ProductRepository $productRepository, AttributeCatalogue $attributeCatalogue){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->attributeCatalogue = $attributeCatalogue;
        $this->initialize();
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' => $this->language
        ]);
    }
        
    
    public function index(Request $request){
        $this->authorize('modules', 'product.index');
        $products = $this->productService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Product'
        ];
        $config['seo'] = __('messages.product');
        $template = 'backend.product.product.index';
        $dropdown = $this->nestedset->Dropdown();
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'dropdown',
            'products',
        ));
    }
    public function create(){ 
        $this->authorize('modules', 'product.create');
        $attributeCatalogue = $this->attributeCatalogue->getAll($this->language);
        $config = $this->configData();
        $config['seo'] = __('messages.product');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.product.product.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'dropdown',
            'attributeCatalogue'
        ));
    }

    public function store(StoreProductRequest $request){
        if($this->productService->create($request, $this->language)){
            return redirect()->route('product.index')->with('success','Thêm mới bản ghi thành công');    
        }
        return redirect()->route('product.index')->with('error','Thêm mới bản ghi thất bại. Hãy thử lại');    
    }

    public function edit($id){
        $this->authorize('modules', 'product.update');
        $products = $this->productRepository->getProductById($id,$this->language);
        $attributeCatalogue = $this->attributeCatalogue->getAll($this->language);
        $config = $this->configData();
        $config['seo'] = __('messages.product');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.product.product.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'products',
            'dropdown',
            'attributeCatalogue'
        ));
    }

    public function update($id,UpdateProductRequest $request){
        if($this->productService->update($id,$request,$this->language)){
            return redirect()->route('product.index')->with('success','Cập nhật bản ghi thành công');    
        }
        return redirect()->route('product.index')->with('error','Cập nhật bản ghi thất bại. Hãy thử lại');    
    }

    public function delete($id){
        $this->authorize('modules', 'product.destroy');
        $products = $this->productRepository->getProductById($id,$this->language);
        $config['seo'] = __('messages.product');
        $template = 'backend.product.product.delete';
        return view('backend.dashboard.layout',compact(
            'template',
            'products',
            'config'
        ));
    }

    public function destroy($id){
        if($this->productService->destroy($id)){
            return redirect()->route('product.index')->with('success','Xóa bản ghi thành công');    
        }
        return redirect()->route('product.index')->with('error','Xóa bản ghi thất bại. Hãy thử lại');  
    }

    private function configData(){
        return [
            'js' => [
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'backend/library/variant.js',
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/nice-select/js/jquery.nice-select.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugin/nice-select/css/nice-select.css',
                'backend/css/plugins/switchery/switchery.css',
            ]
        ];
    }
}
