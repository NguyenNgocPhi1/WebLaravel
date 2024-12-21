<?php

namespace App\Http\Controllers\Backend\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\AttributeServiceInterface as AttributeService;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Http\Requests\Attribute\StoreAttributeRequest;
use App\Http\Requests\Attribute\UpdateAttributeRequest;
// use App\Http\Requests\Attribute\DeleteAttributeRequest;
use App\Classes\Nestedsetbie;
use App\Models\Language;


class AttributeController extends Controller
{
    protected $attributeService;
    protected $attributeRepository;
    protected $language;
    
    public function __construct(AttributeService $attributeService,AttributeRepository $attributeRepository){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->attributeService = $attributeService;
        $this->attributeRepository = $attributeRepository;
        $this->initialize();
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => 'attribute_catalogues',
            'foreignkey' => 'attribute_catalogue_id',
            'language_id' => $this->language
        ]);
    }
        
    
    public function index(Request $request){
        $this->authorize('modules', 'attribute.index');
        $attributes = $this->attributeService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Attribute'
        ];
        $config['seo'] = __('messages.attribute');
        $template = 'backend.attribute.attribute.index';
        $dropdown = $this->nestedset->Dropdown();
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'dropdown',
            'attributes',
        ));
    }
    public function create(){ 
        $this->authorize('modules', 'attribute.create');
        $config = $this->configData();
        $config['seo'] = __('messages.attribute');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.attribute.attribute.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StoreAttributeRequest $request){
        if($this->attributeService->create($request, $this->language)){
            return redirect()->route('attribute.index')->with('success','Thêm mới bản ghi thành công');    
        }
        return redirect()->route('attribute.index')->with('error','Thêm mới bản ghi thất bại. Hãy thử lại');    
    }

    public function edit($id){
        $this->authorize('modules', 'attribute.update');
        $attributes = $this->attributeRepository->getAttributeById($id,$this->language);
        $config = $this->configData();
        $config['seo'] = __('messages.attribute');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.attribute.attribute.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'attributes',
            'dropdown',
        ));
    }

    public function update($id,UpdateAttributeRequest $request){
        if($this->attributeService->update($id,$request,$this->language)){
            return redirect()->route('attribute.index')->with('success','Cập nhật bản ghi thành công');    
        }
        return redirect()->route('attribute.index')->with('error','Cập nhật bản ghi thất bại. Hãy thử lại');    
    }

    public function delete($id){
        $this->authorize('modules', 'attribute.destroy');
        $attributes = $this->attributeRepository->getAttributeById($id,$this->language);
        $config['seo'] = __('messages.attribute');
        $template = 'backend.attribute.attribute.delete';
        return view('backend.dashboard.layout',compact(
            'template',
            'attributes',
            'config'
        ));
    }

    public function destroy($id){
        if($this->attributeService->destroy($id)){
            return redirect()->route('attribute.index')->with('success','Xóa bản ghi thành công');    
        }
        return redirect()->route('attribute.index')->with('error','Xóa bản ghi thất bại. Hãy thử lại');  
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
