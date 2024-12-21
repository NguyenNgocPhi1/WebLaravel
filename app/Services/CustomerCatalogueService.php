<?php

namespace App\Services;

use App\Services\Interfaces\CustomerCatalogueServiceInterface;
use App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface as CustomerCatalogueRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
/**
 * Class CustomerCatalogueService
 * @package App\Services
 */
class CustomerCatalogueService extends BaseService implements CustomerCatalogueServiceInterface
{
    protected $customerCatalogueRepository;
    protected $customereRepository;
    
    public function __construct(CustomerCatalogueRepository $customerCatalogueRepository, CustomerRepository $customerRepository){
        $this->customerCatalogueRepository = $customerCatalogueRepository;
        $this->customerRepository = $customerRepository;
    }
    public function paginate($request){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = ['path' => 'customer/catalogue/index']; 
        $orderBy = [
            'id','asc'
        ];
        $join = []; 
        $relations = ['customers'];

        $customerCatalogues = $this->customerCatalogueRepository->pagination(
            $column,
            $condition,
            $perpage, 
            $extend,
            $orderBy, 
            $join,
            $relations
        );
        return $customerCatalogues;
    }
    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send']);
            $customer = $this->customerCatalogueRepository->create($payload);
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
            $customer = $this->customerCatalogueRepository->update($id,$payload);
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
            $customer = $this->customerCatalogueRepository->delete($id);
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
            'id', 'name', 'description', 'publish'
        ];
    }
}
