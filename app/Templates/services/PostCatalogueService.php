<?php

namespace App\Services;

use App\Services\Interfaces\{$class}CatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\{$class}CatalogueRepositoryInterface as {$class}CatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;

/**
 * Class {$class}CatalogueService
 * @package App\Services
 */
class {$class}CatalogueService extends BaseService implements {$class}CatalogueServiceInterface
{
    protected ${module}CatalogueRepository;
    protected $routerRepository;
    protected $language;
    protected $nestedset;
    protected $controllerName = '{$class}CatalogueController';
    
    public function __construct({$class}CatalogueRepository ${module}CatalogueRepository, RouterRepository $routerRepository){
        $this->{module}CatalogueRepository = ${module}CatalogueRepository;
        $this->routerRepository = $routerRepository;
    }

    
    public function paginate($request, $languageId){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = ['path' => '{module}/catalogue/index']; 
        $orderBy = [
            '{module}_catalogues.lft', 'asc'
        ];
        $join = [
            ['{module}_catalogue_language as tb2','tb2.{module}_catalogue_id','=','{module}_catalogues.id']
        ]; 

        ${module}Catalogue = $this->{module}CatalogueRepository->pagination(
            $column,
            $condition, 
            $perpage, 
            $extend, 
            $orderBy,
            $join
        );
        return ${module}Catalogue;
    }
    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            ${module}Catalogue = $this->createCatalogue($request);
            if(${module}Catalogue->id > 0){
                $this->updateLanguageForCatalogue(${module}Catalogue, $request, $languageId);
                $this->createRouter(${module}Catalogue->id,$request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{module}_catalogues',
                    'foreignkey' => '{module}_catalogue_id',
                    'language_id' => $languageId
                ]);  
                $this->nestedset();   
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }
    public function update($id,$request, $languageId){
        DB::beginTransaction();
        try{
            ${module}Catalogue = $this->{module}CatalogueRepository->findById($id);
            if($this->upload{$class}Catalogue(${module}Catalogue, $request)){
                $this->updateLanguageForCatalogue(${module}Catalogue, $request, $languageId);
                $this->updateRouter($id, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{module}_catalogues',
                    'foreignkey' => '{module}_catalogue_id',
                    'language_id' => $languageId
                ]);  
                $this->nestedset();
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id, $languageId){
        DB::beginTransaction();
        try{
            ${module}Catalogue = $this->{module}CatalogueRepository->delete($id); 
            // $this->routerRepository->forceDeleteByCondition(
            //     ['module_id', '=', $id],
            //     ['language_id', '=', $languageId],
            //     ['controllers', '=', 'App\Http\Controllers\Frontend\\{$class}CatalogueController']
            // );  
            $this->nestedset = new Nestedsetbie([
                'table' => '{module}_catalogues',
                'foreignkey' => '{module}_catalogue_id',
                'language_id' => $languageId
            ]);           
            $this->nestedset();    
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    private function createCatalogue($request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        ${module}Catalogue = $this->{module}CatalogueRepository->create($payload);
        return ${module}Catalogue;
    }

    private function upload{$class}Catalogue(${module}Catalogue, $request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->{module}CatalogueRepository->update(${module}Catalogue->id,$payload);
    }

    private function updateLanguageForCatalogue(${module}Catalogue, $request, $languageId){  
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, ${module}Catalogue->id, $languageId);
        ${module}Catalogue->languages()->detach([$languageId, ${module}Catalogue->id]);
        $language = $this->{module}CatalogueRepository->createPivot(${module}Catalogue, $payload,'languages');
        return $language;
    }

    private function formatLanguagePayload($payload, ${module}CatalogueId, $languageId){
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['{module}_catalogue_id'] = ${module}CatalogueId;
        return $payload;
    }

    

    private function paginateSelect(){
        return [
            '{module}_catalogues.id', 'tb2.name', 'tb2.canonical', '{module}_catalogues.publish', '{module}_catalogues.image','{module}_catalogues.level','{module}_catalogues.order','{module}_catalogues.follow'
        ];
    }

    private function payload(){
        return ['parent_id','follow','publish','image','album'];
    }
    private function payloadLanguage(){
        return ['name','description','content','meta_title','meta_keyword','meta_description','canonical'];
    }
}
