<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\System;
use App\Services\Interfaces\SystemServiceInterface as SystemService;
use App\Repositories\Interfaces\SystemRepositoryInterface as SystemRepository;
use App\Models\Language;

class SystemController extends Controller
{
    protected $systemLibrary;
    protected $systemService;
    protected $systemRepository;
    protected $language;

    public function __construct(System $systemLibrary, SystemService $systemService, SystemRepository $systemRepository){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
        $this->systemLibrary = $systemLibrary;
        $this->systemService = $systemService;
        $this->systemRepository = $systemRepository;
        
    }

    public function index() {
        $systemConfig = $this->systemLibrary->config();
        $systems = convert_array($this->systemRepository->findByCondition(
            [
                ['language_id', '=', $this->language]
            ],true
        ), 'keyword', 'content');
        $config = $this->config();
        $config['seo'] = __('messages.system');
        $template = 'backend.system.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'systemConfig',
            'systems',

        ));
    }
    public function store(Request $request){
        if($this->systemService->save($request, $this->language)){
            return redirect()->route('system.index')->with('success','Cập nhật bản ghi thành công');    
        }
        return redirect()->route('system.index')->with('error','Cập nhật bản ghi thất bại. Hãy thử lại');    
    }

    public function translate($languageId = 0){
        $systemConfig = $this->systemLibrary->config();
        $systems = convert_array($this->systemRepository->findByCondition(
            [
                ['language_id', '=', $languageId]
            ],true
        ), 'keyword', 'content');
        $config = $this->config();
        $config['seo'] = __('messages.system');
        $config['method'] = 'translate';
        $template = 'backend.system.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'systemConfig',
            'languageId',
            'systems',

        ));
    }

    public function saveTranslate(Request $request, $languageId){
        if($this->systemService->save($request, $languageId)){
            return redirect()->route('system.translate', ['languageId' => $languageId])->with('success','Cập nhật bản ghi thành công');    
        }
        return redirect()->route('system.index')->with('error','Cập nhật bản ghi thất bại. Hãy thử lại');    
    }

    private function config(){
        return [
            'js' => [
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/library/finder.js'
            ]
        ];
    }
}
