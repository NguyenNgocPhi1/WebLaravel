<?php

namespace App\Http\Controllers\Backend\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\SourceServiceInterface as SourceService;
use App\Repositories\Interfaces\SourceRepositoryInterface as SourceRepository;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;

use App\Models\Language;

use App\Http\Requests\Source\StoreSourceRequest;
use App\Http\Requests\Source\UpdateSourceRequest;


class SourceController extends Controller
{
    protected $sourceService;
    protected $sourceRepository;
    protected $languageRepository;
    protected $language;
    public function __construct(SourceService $sourceService, SourceRepository $sourceRepository, LanguageRepository $languageRepository){
        $this->sourceService = $sourceService;
        $this->sourceRepository = $sourceRepository;
        $this->languageRepository = $languageRepository;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }
        
    
    public function index(Request $request){
        $this->authorize('modules', 'source.index');
        $sources = $this->sourceService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Source'
        ];
        $config['seo'] = __('messages.source');
        $template = 'backend.source.index';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'sources'
        ));
    }
    public function create(){ 
        $this->authorize('modules', 'source.create');
        $config = $this->config();
        $config['seo'] = __('messages.source');
        $config['method'] = 'create';
        $template = 'backend.source.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
        ));
    }

    public function store(StoreSourceRequest $request){
        if($this->sourceService->create($request, $this->language)){
            return redirect()->route('source.index')->with('success','Thêm mới bản ghi thành công');    
        }
        return redirect()->route('source.index')->with('error','Thêm mới bản ghi thất bại. Hãy thử lại');    
    }

    public function edit($id){
        $this->authorize('modules', 'source.update');
        $source = $this->sourceRepository->findById($id);
        $config = $this->config();
        $config['seo'] = __('messages.source');
        $config['method'] = 'edit';
        $template = 'backend.source.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'source'
        ));
    }

    public function update($id,UpdateSourceRequest $request){
        if($this->sourceService->update($id,$request,$this->language)){
            return redirect()->route('source.index')->with('success','Cập nhật bản ghi thành công');    
        }
        return redirect()->route('source.index')->with('error','Cập nhật bản ghi thất bại. Hãy thử lại');    
    }

    public function delete($id){
        $this->authorize('modules', 'source.destroy');
        $source = $this->sourceRepository->findById($id);
        $config['seo'] = __('messages.source');
        $template = 'backend.source.delete';
        return view('backend.dashboard.layout',compact(
            'template',
            'source',
            'config'
        ));
    }

    public function destroy($id){
        if($this->sourceService->destroy($id)){
            return redirect()->route('source.index')->with('success','Xóa bản ghi thành công');    
        }
        return redirect()->route('source.index')->with('error','Xóa bản ghi thất bại. Hãy thử lại');  
    }

    private function config(){
        return [
            'css' => ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/library/finder.js',
                'backend/library/source.js'
            ]
        ];
    }
    
}
