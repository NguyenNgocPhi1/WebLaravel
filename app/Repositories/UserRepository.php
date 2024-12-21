<?php

namespace App\Repositories;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\User;

/**
 * Class UserService
 * @package App\Services
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model){
        $this->model = $model;
    }
    
    public function userPagination(
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
            
            if(isset($condition['user_catalogue_id']) && $condition['user_catalogue_id'] != 0){
                $query->where('user_catalogue_id', '=', $condition['user_catalogue_id']);
            }
            return $query;
        })->with('user_catalogues');
        if(!empty($join)){
            $query->join(...$join);
        }
        return $query->paginate($perpage)->withQueryString()->withPath(env('APP_URL').$extend['path']);
    }
}
