<?php

namespace App\Services;

use App\Services\Interfaces\AttributeServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
/**
 * Class AttributeService
 * @package App\Services
 */
class AttributeService extends BaseService implements AttributeServiceInterface
{
    protected $attributeRepository;
    protected $routerRepository;
    protected $language;
    
    public function __construct(AttributeRepository $attributeRepository, RouterRepository $routerRepository){
        $this->attributeRepository = $attributeRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'AttributeController';
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
        $extend = [
            'path' => 'attribute/index', 
            'groupBy' => $this->paginateSelect()
        ]; 
        $orderBy = [
            'attributes.id', 'desc'
        ];
        $join = [
            ['attribute_language as tb2','tb2.attribute_id','=','attributes.id'],
            ['attribute_catalogue_attribute as tb3','attributes.id','=','tb3.attribute_id'],
        ]; 
        $relations = ['attribute_catalogues'];
        $rawQuery = $this->whereRaw($request);

        $attribute = $this->attributeRepository->pagination(
            $column,
            $condition, 
            $perpage, 
            $extend, 
            $orderBy,
            $join,
            $relations,
            $rawQuery
        );
        return $attribute;
    }
    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $attribute = $this->createForAttribute($request);
            if($attribute->id > 0){
                $this->updateLanguageForAttribute($attribute,$request, $languageId);
                $this->updateCatalogueForAttribute($attribute,$request);
                $this->createRouter($attribute->id,$request, $this->controllerName, $languageId);
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
            $attribute = $this->attributeRepository->findById($id);
            if($this->uploadAttribute($attribute, $request)){
                $this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->updateCatalogueForAttribute($attribute, $request);
                $this->updateRouter($attribute->id,$request, $this->controllerName, $languageId);
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
            $attribute = $this->attributeRepository->delete($id); //soft delete
            // $this->routerRepository->forceDeleteByCondition(
            //     ['module_id', '=', $id],
            //     ['language_id', '=', $languageId],
            //     ['controllers', '=', 'App\Http\Controllers\Frontend\AttributeController']
            // );  
                    
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            Log::error($e->getMessage());
            // echo $e->getMessage();die();
            return false;
        }
    }


    private function createForAttribute($request){
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $attribute = $this->attributeRepository->create($payload);
        return $attribute;
    }

    private function uploadAttribute($attribute, $request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->attributeRepository->update($attribute->id,$payload);
    }

    private function updateLanguageForAttribute($attribute, $request, $languageId){
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $attribute->id, $languageId);
        $attribute->languages()->detach([$languageId, $attribute->id]);
        return $this->attributeRepository->createPivot($attribute, $payload,'languages');
    }

    private function updateCatalogueForAttribute($attribute, $request){
        return $attribute->attribute_catalogues()->sync($this->catalogue($request));
    }

    private function formatLanguagePayload($payload, $attributeId, $languageId){
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['attribute_id'] = $attributeId;
        return $payload;
    }

    private function catalogue($request){
        if($request->input('catalogue') != null){
            return array_unique(array_merge($request->input('catalogue'),[$request->attribute_catalogue_id]));
        }
        return [$request->attribute_catalogue_id];
    }


    private function paginateSelect(){
        return [
            'attributes.id', 'tb2.name', 'tb2.canonical', 'attributes.publish', 'attributes.image','attributes.order','attributes.follow'
        ];
    }

    private function whereRaw($request){
        $rawCondition = [];
        if($request->integer('attribute_catalogue_id') > 0){
            $rawCondition['whereRaw'] = [
                [
                    'tb3.attribute_catalogue_id IN (
                        SELECT id FROM attribute_catalogues
                        WHERE lft >= (SELECT lft FROM attribute_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM attribute_catalogues as pc WHERE pc.id = ?)
                    )',
                    [$request->integer('attribute_catalogue_id'),$request->integer('attribute_catalogue_id')]
                ]
            ];
        }
        return $rawCondition;
    }

    private function payload(){
        return ['follow','publish','image','album','attribute_catalogue_id'];
    }
    private function payloadLanguage(){
        return ['name','description','content','meta_title','meta_keyword','meta_description','canonical'];
    }
}
