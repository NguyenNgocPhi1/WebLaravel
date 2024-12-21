<?php

namespace App\Services;

use App\Services\Interfaces\ProductCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;

/**
 * Class ProductCatalogueService
 * @package App\Services
 */
class ProductCatalogueService extends BaseService implements ProductCatalogueServiceInterface
{
    protected $productCatalogueRepository;
    protected $routerRepository;
    protected $language;
    protected $nestedset;
    protected $controllerName = 'ProductCatalogueController';
    
    public function __construct(ProductCatalogueRepository $productCatalogueRepository, RouterRepository $routerRepository){
        $this->productCatalogueRepository = $productCatalogueRepository;
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
        $extend = ['path' => 'product/catalogue/index']; 
        $orderBy = [
            'product_catalogues.lft', 'asc'
        ];
        $join = [
            ['product_catalogue_language as tb2','tb2.product_catalogue_id','=','product_catalogues.id']
        ]; 

        $productCatalogue = $this->productCatalogueRepository->pagination(
            $column,
            $condition, 
            $perpage, 
            $extend, 
            $orderBy,
            $join
        );
        return $productCatalogue;
    }
    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $productCatalogue = $this->createCatalogue($request);
            if($productCatalogue->id > 0){
                $this->updateLanguageForCatalogue($productCatalogue, $request, $languageId);
                $this->createRouter($productCatalogue->id,$request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'product_catalogues',
                    'foreignkey' => 'product_catalogue_id',
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
            $productCatalogue = $this->productCatalogueRepository->findById($id);
            if($this->uploadProductCatalogue($productCatalogue, $request)){
                $this->updateLanguageForCatalogue($productCatalogue, $request, $languageId);
                $this->updateRouter($id, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'product_catalogues',
                    'foreignkey' => 'product_catalogue_id',
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
            $productCatalogue = $this->productCatalogueRepository->delete($id); 
            // $this->routerRepository->forceDeleteByCondition(
            //     ['module_id', '=', $id],
            //     ['controllers', '=', 'App\Http\Controllers\Frontend\ProductCatalogueController']
            // );  
            $this->nestedset = new Nestedsetbie([
                'table' => 'product_catalogues',
                'foreignkey' => 'product_catalogue_id',
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
        $productCatalogue = $this->productCatalogueRepository->create($payload);
        return $productCatalogue;
    }

    private function uploadProductCatalogue($productCatalogue, $request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->productCatalogueRepository->update($productCatalogue->id,$payload);
    }

    private function updateLanguageForCatalogue($productCatalogue, $request, $languageId){  
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $productCatalogue->id, $languageId);
        $productCatalogue->languages()->detach([$languageId, $productCatalogue->id]);
        $language = $this->productCatalogueRepository->createPivot($productCatalogue, $payload,'languages');
        return $language;
    }

    private function formatLanguagePayload($payload, $productCatalogueId, $languageId){
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['product_catalogue_id'] = $productCatalogueId;
        return $payload;
    }

    

    private function paginateSelect(){
        return [
            'product_catalogues.id', 'tb2.name', 'tb2.canonical', 'product_catalogues.publish', 'product_catalogues.image','product_catalogues.level','product_catalogues.order','product_catalogues.follow'
        ];
    }

    private function payload(){
        return ['parent_id','follow','publish','image','album'];
    }
    private function payloadLanguage(){
        return ['name','description','content','meta_title','meta_keyword','meta_description','canonical'];
    }
}
