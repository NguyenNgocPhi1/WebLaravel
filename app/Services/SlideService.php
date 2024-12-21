<?php

namespace App\Services;

use App\Services\Interfaces\SlideServiceInterface;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Services\BaseService;

/**
 * Class SlideService
 * @package App\Services
 */
class SlideService extends BaseService implements SlideServiceInterface
{

    public function __construct(SlideRepository $slideRepository){
        $this->slideRepository = $slideRepository;
    }
    
    public function paginate($request){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = ['path' => 'slide/index']; 

        $slides = $this->slideRepository->pagination(
            $column,
            $condition, 
            $perpage,
            ['path' => 'slide/index'], 
        );
        return $slides;
    }
    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $payload = $request->only(['_token','name', 'keyword', 'setting', 'short_code']);
            $payload['item'] = $this->handleSlideItem($request, $languageId);
            $slide = $this->slideRepository->create($payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }
    public function update($id, $request, $languageId){
        DB::beginTransaction();
        try{
            $slide = $this->slideRepository->findById($id);
            $slideItem = $slide->item;
            unset($slideItem[$languageId]);
            $payload = $request->only(['_token','name', 'keyword', 'setting', 'short_code']);
            $payload['item'] = $this->handleSlideItem($request, $languageId) + $slideItem;
            $slide = $this->slideRepository->update($id,$payload);
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
            $slide = $this->slideRepository->delete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateSlideOrder($post, $language){
        $slideId = $post[0]['id'];
        
        $temp = array_map(function($item){
            unset($item['id']);
            return $item;
        }, $post);
        $slide = $this->slideRepository->findById($slideId);
        $slideItem = $slide->item;
        unset($slideItem[$language]);

        $payload['item'][$language] = $temp + $slideItem;
        $slide = $this->slideRepository->update($slideId, $payload);
    }

    private function handleSlideItem($request, $languageId){
        $slide = $request->input('slide');
        $temp = [];
        foreach($slide['image'] as $key => $val){
            $temp[$languageId][] = [
                'image' => $val,
                'name' => $slide['name'][$key],
                'description' => $slide['description'][$key],
                'canonical' => $slide['canonical'][$key],
                'alt' => $slide['alt'][$key],
                'window' => (isset($slide['window'][$key])) ? $slide['window'][$key] : ''
            ];
        }
        return $temp;
    }

    public function convertSlideArray(array $slide = []): array{
        $temp = [];
        $fields = ['image', 'description', 'window', 'canonical', 'name', 'alt'];
        foreach($slide as $key => $val){
            foreach($fields as $field){
                $temp[$field][] = $val[$field];
            }
        }
        return $temp;
    }   

    private function paginateSelect(){
        return [
            'id', 
            'name',
            'keyword',
            'description',
            'item',
            'setting',
            'short_code',
            'publish',
        ];
    }

    public function getSlide($array = [], $language = 1){
        $slides = $this->slideRepository->findByCondition(...$this->getSlideAgrument($array));
        $temp = [];
        foreach ($slides as $key => $val) {
            $temp[$val->keyword]['item'] = $val->item[$language];
            $temp[$val->keyword]['setting'] = $val->setting;
        }
        return $temp;
    }

    private function getSlideAgrument($array){
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => [],
            'orderBy' => ['id', 'desc'],
            'param' => [
                'whereIn' => $array,
                'whereInField' => 'keyword'
            ]
        ];
    }
}
