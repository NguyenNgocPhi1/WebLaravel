<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;



class RouterController extends FrontendController
{
    protected $routerRepository;
    protected $router;
    
    public function __construct(
        RouterRepository $routerRepository
    ){
        parent::__construct();
        $this->routerRepository = $routerRepository;
        
    }

    public function index(string $canonical = '', Request $request){
        $this->getRouter($canonical);
        if(!is_null($this->router) && !empty($this->router)){
            $method = 'index';
            echo app($this->router->controllers)->{$method}($this->router->module_id, $request);
        }
    }
    
    public function page(string $canonical = '', $page = 1, Request $request){
        $page = (!isset($page)) ? 1 : $page;
        $this->getRouter($canonical);
        if(!is_null($this->router) && !empty($this->router)){
            $method = 'index';
            echo app($this->router->controllers)->{$method}($this->router->module_id, $request, $page);
        }
    }

    public function getRouter($canonical){
        $this->router = $this->routerRepository->findByCondition(
            [
                ['canonical', '=', $canonical],
                ['language_id', '=', $this->language],
            ]
        );   
    }
}
