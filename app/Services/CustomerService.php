<?php

namespace App\Services;

use App\Services\Interfaces\CustomerServiceInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Services\BaseService;

/**
 * Class CustomerService
 * @package App\Services
 */
class CustomerService extends BaseService implements CustomerServiceInterface
{

    public function __construct(CustomerRepository $customerRepository){
        $this->customerRepository = $customerRepository;
    }
    
    public function paginate($request){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
            'customer_catalogue_id' => $request->integer('customer_catalogue_id'),
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = ['path' => 'customer/index']; 

        $customers = $this->customerRepository->customerPagination(
            $column,
            $condition, 
            $perpage,
            $extend, 
        );
        return $customers;
    }
    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send','re_password']);
            if($payload['birthday'] != null){
                $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            }
            $payload['password'] = Hash::make($payload['password']);
            $customer = $this->customerRepository->create($payload);
            DB::commit();
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
            $payload = $request->except(['_token','send']);
            if($payload['birthday'] != null){
                $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            }
            $customer = $this->customerRepository->update($id,$payload);
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
            $customer = $this->customerRepository->delete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    private function convertBirthdayDate($birthday = ''){
        $carbonDate = Carbon::createFromFormat('Y-m-d', $birthday);
        $birthday = $carbonDate->format('Y-m-d H:i:s');
        return $birthday;
    }

    private function paginateSelect(){
        return [
            'id', 'name', 'email', 'phone', 'address', 'publish', 'customer_catalogue_id', 'image', 'source_id'
        ];
    }
}
