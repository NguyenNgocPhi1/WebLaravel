<?php

namespace App\Services;

use App\Services\Interfaces\MenuCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MenuCatalogueService extends BaseService implements MenuCatalogueServiceInterface
{
    protected $menuCatalogueRepository;
    
    public function __construct(MenuCatalogueRepository $menuCatalogueRepository){
        $this->menuCatalogueRepository = $menuCatalogueRepository;
    }
    public function paginate($request){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = [
            'path' => 'menu/index', 
        ]; 
        $menuCatalogues = $this->menuCatalogueRepository->pagination(
            $column,
            $condition, 
            $perpage, 
            $extend, 
        );
        return $menuCatalogues;
    }
    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->only('name','keyword');
            $payload['keyword'] = Str::slug($payload['keyword']);
            $menuCatalogue = $this->menuCatalogueRepository->create($payload);
            DB::commit();
            return [
                'name' => $menuCatalogue->name,
                'id' => $menuCatalogue->id
            ];
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }
    public function update($id,$request){
        DB::beginTransaction();
        try{
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            Log::error($e->getMessage());
            // echo $e->getMessage();die();
            return false;
        }
    }


    private function paginateSelect(){
        return [
            'id',
            'name',
            'keyword',
            'publish'
        ];
    }

}
