<?php

namespace App\Services;
use App\Services\Interfaces\BaseServiceInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Language;



/**
 * Class BaseService
 * @package App\Services
 */
class BaseService implements BaseServiceInterface
{
    
    
    public function __construct(){
    }


    public function currentLanguage(){
        $locale = app()->getLocale();
        $language = Language::where('canonical', $locale)->first();
        return $language->id;
    }

    public function formatAlbum($request){
        return ($request->input('album') && !empty($request->input('album'))) ? json_encode($request->input('album')) : '';
    }

    public function formatJson($request, $inputName){
        return ($request->input($inputName) && !empty($request->input($inputName))) ? json_encode($request->input($inputName)) : '';
    }

    public function nestedset(){
        $this->nestedset->Get('level ASC, order ASC');            
        $this->nestedset->Recursive(0, $this->nestedset->Set());            
        $this->nestedset->Action();
    }

    public function formatRouterPayload($modelId, $request, $controllerName, $languageId){
        $router = [
            'canonical' => Str::slug($request->input('canonical')),
            'module_id' => $modelId,
            'language_id' => $languageId,
            'controllers' => 'App\Http\Controllers\Frontend\\'.$controllerName.''
        ];
        return $router;
    }

    public function createRouter($modelId, $request, $controllerName, $languageId){
        $router = $this->formatRouterPayload($modelId, $request, $controllerName, $languageId);
        $this->routerRepository->create($router);
    }

    public function updateRouter($modelId, $request, $controllerName, $languageId){
        $payloadRouter = $this->formatRouterPayload($modelId, $request, $controllerName, $languageId);
        $condition = [
            ['module_id','=',$modelId],
            ['language_id','=',$languageId],
            ['controllers','=', 'App\Http\Controllers\Frontend\\'.$controllerName.''],
        ];
        $router = $this->routerRepository->findByCondition($condition);
        return $this->routerRepository->update($router->id, $payloadRouter);
    }

    public function updateStatus($post = []){
        DB::beginTransaction();
        try{
            $model = lcfirst($post['model']).'Repository';
            $modelService = lcfirst($post['model']).'Service';
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);
            $user = $this->{$model}->update($post['modelId'], $payload);
            if($model == 'userCatalogueRepository'){
                $this->changeUserStatus($post, $payload[$post['field']]);
            }
            
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatusAll($post = []){
        DB::beginTransaction();
        try{
            $model = lcfirst($post['model']).'Repository';
            $modelService = lcfirst($post['model']).'Service';
            $payload[$post['field']] = $post['value'];
            $flag = $this->{$model}->updateWhereIn('id', $post['id'],$payload);
            if($model == 'userCatalogueRepository'){
                $this->changeUserStatus($post, $post['value']);
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

}
