<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use App\Services\Interfaces\SlideServiceInterface as SlideService;
use App\Enums\SlideEnum;



class HomeController extends FrontendController
{
    protected $slideRepository;
    protected $widgetService;
    protected $slideService;
    
    public function __construct(
        SlideRepository $slideRepository,
        WidgetService $widgetService,
        SlideService $slideService,
    ){
        parent::__construct();
        $this->slideRepository = $slideRepository;
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
    }

    public function index(Request $request){
        
        $config = $this->config();
        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'category', 'countObject' => true],
            ['keyword' => 'category-highlight'],
            ['keyword' => 'category-home', 'children' => true, 'promotion' => true, 'object' => true],
            ['keyword' => 'bestseller'],
        ], $this->language);
        $slides = $this->slideService->getSlide([SlideEnum::BANNER, SlideEnum::MAIN_SLIDE], $this->language);
        $system = $this->system;
        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => $this->system['seo_meta_images'],
            'canonical' => config('app.url'),
        ];
        return view('frontend.homepage.home.index', compact(
            'config', 
            'slides',
            'widgets',
            'seo',
            'system',
        ));
    }

    private function config(){
        return [
            'language' => $this->language
        ];
    }
}
