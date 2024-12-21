<?php

namespace App\Services;

use App\Services\Interfaces\AttributeCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class AttributeCatalogueService extends BaseService implements AttributeCatalogueServiceInterface
{
    protected $attributeCatalogueRepository;
    protected $routerRepository;
    protected $language;
    protected $nestedset;
    protected $controllerName = 'AttributeCatalogueController';
    
    public function __construct(AttributeCatalogueRepository $attributeCatalogueRepository, RouterRepository $routerRepository){
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
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
        $extend = ['path' => 'attribute/catalogue/index']; 
        $orderBy = [
            'attribute_catalogues.lft', 'asc'
        ];
        $join = [
            ['attribute_catalogue_language as tb2','tb2.attribute_catalogue_id','=','attribute_catalogues.id']
        ]; 

        $attributeCatalogue = $this->attributeCatalogueRepository->pagination(
            $column,
            $condition, 
            $perpage, 
            $extend, 
            $orderBy,
            $join
        );
        return $attributeCatalogue;
    }
    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $attributeCatalogue = $this->createCatalogue($request);
            if($attributeCatalogue->id > 0){
                $this->updateLanguageForCatalogue($attributeCatalogue, $request, $languageId);
                $this->createRouter($attributeCatalogue->id,$request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'attribute_catalogues',
                    'foreignkey' => 'attribute_catalogue_id',
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
            $attributeCatalogue = $this->attributeCatalogueRepository->findById($id);
            if($this->uploadAttributeCatalogue($attributeCatalogue, $request)){
                $this->updateLanguageForCatalogue($attributeCatalogue, $request, $languageId);
                $this->updateRouter($id, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'attribute_catalogues',
                    'foreignkey' => 'attribute_catalogue_id',
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
            $attributeCatalogue = $this->attributeCatalogueRepository->delete($id); 
            // $this->routerRepository->forceDeleteByCondition(
            //     ['module_id', '=', $id],
            //     ['language_id', '=', $languageId],
            //     ['controllers', '=', 'App\Http\Controllers\Frontend\AttributeCatalogueController']
            // );  
            $this->nestedset = new Nestedsetbie([
                'table' => 'attribute_catalogues',
                'foreignkey' => 'attribute_catalogue_id',
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
        $attributeCatalogue = $this->attributeCatalogueRepository->create($payload);
        return $attributeCatalogue;
    }

    private function uploadAttributeCatalogue($attributeCatalogue, $request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->attributeCatalogueRepository->update($attributeCatalogue->id,$payload);
    }

    private function updateLanguageForCatalogue($attributeCatalogue, $request, $languageId){  
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $attributeCatalogue->id, $languageId);
        $attributeCatalogue->languages()->detach([$languageId, $attributeCatalogue->id]);
        $language = $this->attributeCatalogueRepository->createPivot($attributeCatalogue, $payload,'languages');
        return $language;
    }

    private function formatLanguagePayload($payload, $attributeCatalogueId, $languageId){
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['attribute_catalogue_id'] = $attributeCatalogueId;
        return $payload;
    }

    

    private function paginateSelect(){
        return [
            'attribute_catalogues.id', 'tb2.name', 'tb2.canonical', 'attribute_catalogues.publish', 'attribute_catalogues.image','attribute_catalogues.level','attribute_catalogues.order','attribute_catalogues.follow'
        ];
    }

    private function payload(){
        return ['parent_id','follow','publish','image','album'];
    }
    private function payloadLanguage(){
        return ['name','description','content','meta_title','meta_keyword','meta_description','canonical'];
    }
}
