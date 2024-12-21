<?php

namespace App\Services;

use App\Services\Interfaces\PromotionServiceInterface;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Services\BaseService;
use App\Enums\PromotionEnum;

/**
 * Class PromotionService
 * @package App\Services
 */
class PromotionService extends BaseService implements PromotionServiceInterface
{

    public function __construct(PromotionRepository $promotionRepository){
        $this->promotionRepository = $promotionRepository;
    }
    
    public function paginate($request){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = ['path' => 'promotion/index']; 

        $promotions = $this->promotionRepository->pagination(
            $column,
            $condition, 
            $perpage,
            $extend, 
        );
        return $promotions;
    }

    private function request($request){
        $payload = $request->only(
            'name', 
            'code', 
            'description', 
            'method', 
            'module_type', 
            'startDate', 
            'endDate',
            'neverEndDate',
        );
        $payload['maxDiscountValue'] = convert_price($request->input(PromotionEnum::PRODUCT_AND_QUANTITY.'.maxDiscountValue'));
        $payload['discountValue'] = convert_price($request->input(PromotionEnum::PRODUCT_AND_QUANTITY.'.discountValue'));
        $payload['discountType'] = $request->input(PromotionEnum::PRODUCT_AND_QUANTITY.'.discountType');
        $payload['startDate'] = Carbon::createFromFormat('d/m/Y H:i', $payload['startDate']);
        if(isset($payload['endDate'])){
            $payload['endDate'] = Carbon::createFromFormat('d/m/Y H:i', $payload['endDate']);
        }
        $payload['code'] = (empty($payload['code'])) ? time() : $payload['code'];
        switch ($payload['method']) {
            case PromotionEnum::ORDER_AMOUNT_RANGE:
                $payload[PromotionEnum::DISCOUNT_INFORMATION] = $this->orderByRange($request);
                break;
            case PromotionEnum::PRODUCT_AND_QUANTITY:
                $payload[PromotionEnum::DISCOUNT_INFORMATION] = $this->productAndQuantity($request);
                break;
            default:
                break;
        }
        return $payload;
    }

    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $payload = $this->request($request);
            $promotion = $this->promotionRepository->create($payload);
            if($promotion->id > 0){
                $this->handleRelation($request, $promotion);
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
            $payload = $this->request($request);
            $promotion = $this->promotionRepository->update($id,$payload);
            $this->handleRelation($request, $promotion, 'update');
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    private  function handleRelation($request, $promotion, $method = 'create'){
        if($request->input('method') === PromotionEnum::PRODUCT_AND_QUANTITY){
            $object = $request->input('object');
            $payload = [];
            if(!is_null($object)){
                foreach ($object['id'] as $key => $val) {
                    $payload[] = [
                        'product_id' => $val,
                        // 'product_variant_id' => $object['product_variant_id'][$key],
                        'variant_uuid' => $object['variant_uuid'][$key],
                        'model' => $request->input(PromotionEnum::MODULE_TYPE)
                    ];
                }
            }
            if($method == 'update'){
                $promotion->products()->detach();
            }
            $promotion->products()->sync($payload);   
                     
        }
    }

    private function handleSourceAndCondition($request){
        $data = [
            'source' => [
                'status' => $request->input('source'),
                'data' => $request->input('sourceValue'),
            ],
            'apply' => [
                'status' => $request->input('applyStatus'),
                'data' => $request->input('applyValue'),
            ]
        ];
        if(!is_null($data['apply']['data'])){
            foreach ($data['apply']['data'] as $key => $val) {
                $data['apply']['condition'][$val] = $request->input($val);
            }
        }
        return $data;
    }

    private function orderByRange($request){
        $data['info'] = $request->input('promotion_order_amount_range');
        return $data + $this->handleSourceAndCondition($request);
    }

    private function productAndQuantity($request){
        $data['info'] = $request->input('product_and_quantity');
        $data['info']['model'] = $request->input(PromotionEnum::MODULE_TYPE);
        $data['info']['object'] = $request->input('object');
        return $data + $this->handleSourceAndCondition($request);
    }

    


    public function destroy($id){
        DB::beginTransaction();
        try{
            $promotion = $this->promotionRepository->delete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function saveTranslate($request, $languageId){
        DB::beginTransaction();
        try{
            $temp = [];
            $translateId = $request->input('translateId');
            $promotion = $this->promotionRepository->findById($request->input('promotionId'));
            $temp = $promotion->description;
            $temp[$translateId] = $request->input('translate_description');
            $payload['description'] = $temp;
            $this->promotionRepository->update($promotion->id, $payload);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    private function paginateSelect(){
        return [
            'id', 
            'name', 
            'code', 
            'discountInformation', 
            'description',
            'method', 
            'neverEndDate', 
            'startDate', 
            'endDate', 
            'publish', 
            'order', 
        ];
    }
}
