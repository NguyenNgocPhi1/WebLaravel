<?php

namespace App\Services;

use App\Services\Interfaces\PostServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
/**
 * Class PostService
 * @package App\Services
 */
class PostService extends BaseService implements PostServiceInterface
{
    protected $postRepository;
    protected $routerRepository;
    protected $language;
    
    public function __construct(PostRepository $postRepository, RouterRepository $routerRepository){
        $this->postRepository = $postRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'PostController';
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
            'path' => 'post/index', 
            'groupBy' => $this->paginateSelect()
        ]; 
        $orderBy = [
            'posts.id', 'desc'
        ];
        $join = [
            ['post_language as tb2','tb2.post_id','=','posts.id'],
            ['post_catalogue_post as tb3','posts.id','=','tb3.post_id'],
        ]; 
        $relations = ['post_catalogues'];
        $rawQuery = $this->whereRaw($request);

        $post = $this->postRepository->pagination(
            $column,
            $condition, 
            $perpage, 
            $extend, 
            $orderBy,
            $join,
            $relations,
            $rawQuery
        );
        return $post;
    }
    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $post = $this->createForPost($request);

            if($post->id > 0){
                $this->updateLanguageForPost($post,$request, $languageId);
                $this->updateCatalogueForPost($post,$request);
                $this->createRouter($post->id,$request, $this->controllerName, $languageId);
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
            $post = $this->postRepository->findById($id);
            if($this->uploadPost($post, $request)){
                $this->updateLanguageForPost($post, $request, $languageId);
                $this->updateCatalogueForPost($post, $request);
                $this->updateRouter($post->id,$request, $this->controllerName, $languageId);
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
            $post = $this->postRepository->delete($id); //soft delete
            // $this->routerRepository->forceDeleteByCondition(
            //     ['module_id', '=', $id],
            //     ['language_id', '=', $languageId],
            //     ['controllers', '=', 'App\Http\Controllers\Frontend\PostController']
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

    private function createForPost($request){
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $post = $this->postRepository->create($payload);
        return $post;
    }

    private function uploadPost($post, $request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->postRepository->update($post->id,$payload);
    }

    private function updateLanguageForPost($post, $request, $languageId){
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $post->id, $languageId);
        $post->languages()->detach([$languageId, $post->id]);
        return $this->postRepository->createPivot($post, $payload,'languages');
    }

    private function updateCatalogueForPost($post, $request){
        return $post->post_catalogues()->sync($this->catalogue($request));
    }

    private function formatLanguagePayload($payload, $postId, $languageId){
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['post_id'] = $postId;
        return $payload;
    }

    private function catalogue($request){
        if($request->input('catalogue') != null){
            return array_unique(array_merge($request->input('catalogue'),[$request->post_catalogue_id]));
        }
        return [$request->post_catalogue_id];
    }


    private function paginateSelect(){
        return [
            'posts.id', 'tb2.name', 'tb2.canonical', 'posts.publish', 'posts.image','posts.order','posts.follow'
        ];
    }

    private function whereRaw($request){
        $rawCondition = [];
        if($request->integer('post_catalogue_id') > 0){
            $rawCondition['whereRaw'] = [
                [
                    'tb3.post_catalogue_id IN (
                        SELECT id FROM post_catalogues
                        WHERE lft >= (SELECT lft FROM post_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM post_catalogues as pc WHERE pc.id = ?)
                    )',
                    [$request->integer('post_catalogue_id'),$request->integer('post_catalogue_id')]
                ]
            ];
        }
        return $rawCondition;
    }

    private function payload(){
        return ['follow','publish','image','album','post_catalogue_id'];
    }
    private function payloadLanguage(){
        return ['name','description','content','meta_title','meta_keyword','meta_description','canonical'];
    }
}
