<?php

namespace App\Services;

use App\Services\Interfaces\UserCatalogueServiceInterface;
use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as UserCatalogueRepository;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
/**
 * Class UserCatalogueService
 * @package App\Services
 */
class UserCatalogueService extends BaseService implements UserCatalogueServiceInterface
{
    protected $userCatalogueRepository;
    protected $usereRepository;
    
    public function __construct(UserCatalogueRepository $userCatalogueRepository, UserRepository $userRepository){
        $this->userCatalogueRepository = $userCatalogueRepository;
        $this->userRepository = $userRepository;
    }
    public function paginate($request){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = ['path' => 'user/catalogue/index']; 
        $orderBy = [
            'id','asc'
        ];
        $join = []; 
        $relations = ['users'];

        $userCatalogues = $this->userCatalogueRepository->pagination(
            $column,
            $condition,
            $perpage, 
            $extend,
            $orderBy, 
            $join,
            $relations
        );
        return $userCatalogues;
    }
    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send']);
            $user = $this->userCatalogueRepository->create($payload);
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
            $user = $this->userCatalogueRepository->update($id,$payload);
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
            $user = $this->userCatalogueRepository->delete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    

    public function changeUserStatus($post, $value){
        DB::beginTransaction();
        try{
            $array = [];
            if(isset($post['modelId'])){
                $array[] = $post['modelId'];
            }else{
                $array = $post['id'];
            }
            $payload[$post['field']] = $value;
            $this->userRepository->updateWhereIn('user_catalogue_id', $array, $payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function setPermission($request){
        DB::beginTransaction();
        try{
            $permissions = $request->input('permission');
            if(count($permissions))
            foreach($permissions as $key => $val){
                $userCatalogues = $this->userCatalogueRepository->findById($key);
                $userCatalogues->permissions()->detach();
                $userCatalogues->permissions()->sync($val);
            }
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
