<?php

namespace App\Services;

use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;

/**
 * Class PostCatalogueService
 * @package App\Services
 */
class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{
    protected $postCatalogueRepository;
    protected $routerRepository;
    protected $language;
    protected $controllerName = 'PostCatalogueController';
    
    public function __construct(PostCatalogueRepository $postCatalogueRepository, RouterRepository $routerRepository){
        $this->postCatalogueRepository = $postCatalogueRepository;
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
        $extend = ['path' => 'post/catalogue/index']; 
        $orderBy = [
            'post_catalogues.lft', 'asc'
        ];
        $join = [
            ['post_catalogue_language as tb2','tb2.post_catalogue_id','=','post_catalogues.id']
        ]; 

        $postCatalogue = $this->postCatalogueRepository->pagination(
            $column,
            $condition, 
            $perpage, 
            $extend, 
            $orderBy,
            $join
        );
        return $postCatalogue;
    }
    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $postCatalogue = $this->createCatalogue($request);
            if($postCatalogue->id > 0){
                $this->updateLanguageForCatalogue($postCatalogue, $request, $languageId);
                $this->createRouter($postCatalogue->id,$request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'post_catalogues',
                    'foreignkey' => 'post_catalogue_id',
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
            $postCatalogue = $this->postCatalogueRepository->findById($id);
            if($this->uploadPostCatalogue($postCatalogue, $request)){
                $this->updateLanguageForCatalogue($postCatalogue, $request, $languageId);
                $this->updateRouter($id, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'post_catalogues',
                    'foreignkey' => 'post_catalogue_id',
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

    public function destroy($id){
        DB::beginTransaction();
        try{
            $postCatalogue = $this->postCatalogueRepository->delete($id); 
            // $this->routerRepository->forceDeleteByCondition(
            //     ['module_id', '=', $id],
            //     ['language_id', '=', $languageId],
            //     ['controllers', '=', 'App\Http\Controllers\Frontend\PostCatalogueController']
            // );      
            $this->nestedset = new Nestedsetbie([
                'table' => 'post_catalogues',
                'foreignkey' => 'post_catalogue_id',
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
        $postCatalogue = $this->postCatalogueRepository->create($payload);
        return $postCatalogue;
    }

    private function uploadPostCatalogue($postCatalogue, $request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->postCatalogueRepository->update($postCatalogue->id,$payload);
    }

    private function updateLanguageForCatalogue($postCatalogue, $request, $languageId){  
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $postCatalogue->id, $languageId);
        $postCatalogue->languages()->detach([$languageId, $postCatalogue->id]);
        $language = $this->postCatalogueRepository->createPivot($postCatalogue, $payload,'languages');
        return $language;
    }

    private function formatLanguagePayload($payload, $postCatalogueId, $languageId){
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['post_catalogue_id'] = $postCatalogueId;
        return $payload;
    }

    

    private function paginateSelect(){
        return [
            'post_catalogues.id', 'tb2.name', 'tb2.canonical', 'post_catalogues.publish', 'post_catalogues.image','post_catalogues.level','post_catalogues.order','post_catalogues.follow'
        ];
    }

    private function payload(){
        return ['parent_id','follow','publish','image','album'];
    }
    private function payloadLanguage(){
        return ['name','description','content','meta_title','meta_keyword','meta_description','canonical'];
    }
}
