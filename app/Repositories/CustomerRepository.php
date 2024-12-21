<?php

namespace App\Repositories;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Customer;

/**
 * Class CustomerService
 * @package App\Services
 */
class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    protected $model;

    public function __construct(Customer $model){
        $this->model = $model;
    }
    
    public function customerPagination(
        array $column = ['*'], 
        array $condition= [], 
        int $perpage = 1, 
        array $extend = [], 
        array $orderBy = ['id','desc'],
        array $join = [], 
        array $relations = [],
        array $rawQuery = []
    ){
        $query =  $this->model->select($column)->where(function($query) use ($condition){
            if(isset($condition['keyword']) && !empty($condition['keyword'])){
                $query->where('name', 'LIKE', '%'.$condition['keyword'].'%')->orWhere('email', 'LIKE', '%'.$condition['keyword'].'%')
                ->orWhere('phone', 'LIKE', '%'.$condition['keyword'].'%')->orWhere('address', 'LIKE', '%'.$condition['keyword'].'%');
            }

            if(isset($condition['publish']) && $condition['publish'] != 0){
                $query->where('publish', '=', $condition['publish']);
            }
            if(isset($condition['customer_catalogue_id']) && $condition['customer_catalogue_id'] != 0){
                $query->where('customer_catalogue_id', '=', $condition['customer_catalogue_id']);
            }
            return $query;
        })->with(['customer_catalogues', 'sources']);
        if(!empty($join)){
            $query->join(...$join);
        }
        return $query->paginate($perpage)->withQueryString()->withPath(env('APP_URL').$extend['path']);
    }
}
