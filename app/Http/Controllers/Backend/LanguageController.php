<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\LanguageServiceInterface as LanguageService;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use App\Http\Requests\Language\StoreLanguageRequest;
use App\Http\Requests\Language\UpdateLanguageRequest;
use App\Http\Requests\TranslateRequest;

class LanguageController extends Controller
{
    protected $languageService;
    protected $languageRepository;
    
    public function __construct(LanguageService $languageService,LanguageRepository $languageRepository){
        $this->languageService = $languageService;
        $this->languageRepository = $languageRepository;
    }
        
    
    public function index(Request $request){
        $this->authorize('modules', 'language.index');
        $language = $this->languageService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Language'
        ];
        $config['seo'] = __('messages.language');
        $template = 'backend.language.index';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'language'
        ));
    }
    public function create(){ 
        $this->authorize('modules', 'language.create');
        $config = $this->configData();
        $config['seo'] = __('messages.language');
        $config['method'] = 'create';
        $template = 'backend.language.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
        ));
    }

    public function store(StoreLanguageRequest $request){
        if($this->languageService->create($request)){
            return redirect()->route('language.index')->with('success','Thêm mới bản ghi thành công');    
        }
        return redirect()->route('language.index')->with('error','Thêm mới bản ghi thất bại. Hãy thử lại');    
    }

    public function edit($id){
        $this->authorize('modules', 'language.update');
        $language = $this->languageRepository->findById($id);
        $config = $this->configData();
        $config['seo'] = __('messages.language');
        $config['method'] = 'edit';
        $template = 'backend.language.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'language'
        ));
    }

    public function update($id,UpdateLanguageRequest $request){
        if($this->languageService->update($id,$request)){
            return redirect()->route('language.index')->with('success','Cập nhật bản ghi thành công');    
        }
        return redirect()->route('language.index')->with('error','Cập nhật bản ghi thất bại. Hãy thử lại');    
    }

    public function delete($id){
        $this->authorize('modules', 'language.destroy');
        $language = $this->languageRepository->findById($id);
        $config['seo'] = __('messages.language');
        $template = 'backend.language.delete';
        return view('backend.dashboard.layout',compact(
            'template',
            'language',
            'config'
        ));
    }

    public function destroy($id){
        if($this->languageService->destroy($id)){
            return redirect()->route('language.index')->with('success','Xóa bản ghi thành công');    
        }
        return redirect()->route('language.index')->with('error','Xóa bản ghi thất bại. Hãy thử lại');  
    }

    private function configData(){
        return [
            'js' => [
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/library/finder.js'
            ]
        ];
    }
    
    public function switchBackendLanguage($id){
        $language = $this->languageRepository->findById($id);
        if($this->languageService->switch($id)){
            session(['app_locale' => $language->canonical]);
            \App::setLocale($language->canonical);
        }
        return redirect()->back();  
    }

    public function translate($id = 0, $languageId = 0, $model = ''){
        $language = $this->languageRepository->findById($languageId);
        $repositoryInstance = $this->repositoryInstance($model);
        $languageInstance = $this->repositoryInstance('Language');
        $currentLanguage = $languageInstance->findByCondition([
            ['canonical','=',session('app_locale')]
        ]);
        $methodName = 'get'.$model.'ById';
        $object = $repositoryInstance->{$methodName}($id, $currentLanguage->id);
        $objectTranslate = $repositoryInstance->{$methodName}($id, $languageId);
        $this->authorize('modules', 'language.translate');
        $config = [
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
        $option = [
            'id' => $id,
            'languageId' => $languageId,
            'model' => $model
        ];
        $config['seo'] = __('messages.language');
        $template = 'backend.language.translate';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'object',
            'objectTranslate',
            'option',
            'language',
        ));
    }

    public function storeTranslate(TranslateRequest $request){
        $option = $request->input('option');
        if($this->languageService->saveTranslate($option,$request)){
            return redirect()->back()->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->back()->with('error','Cập nhật bản ghi thất bại. Hãy thử lại');
    }

    private function repositoryInstance($model){
        $repositoryNamespace = '\App\Repositories\\' . ucfirst($model) . 'Repository';
        if(class_exists($repositoryNamespace)){
            $repositoryInstance = app($repositoryNamespace);
        }
        return $repositoryInstance ?? null;
    }
    
}
