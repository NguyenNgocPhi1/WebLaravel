<?php

namespace App\Services;

use App\Services\Interfaces\PermissionServiceInterface;
use App\Repositories\Interfaces\PermissionRepositoryInterface as PermissionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Class PermissionService
 * @package App\Services
 */
class PermissionService implements PermissionServiceInterface
{
    protected $permissionRepository;
    
    public function __construct(PermissionRepository $permissionRepository){
        $this->permissionRepository = $permissionRepository;
    }
    public function paginate($request){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = [
            'path' => 'permission/index', 
        ]; 
        $permission = $this->permissionRepository->pagination(
            $column,
            $condition, 
            $perpage, 
            $extend, 
        );
        return $permission;
    }
    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send']);
            $payload['user_id'] = Auth::id();
            $permission = $this->permissionRepository->create($payload);
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
            $permission = $this->permissionRepository->update($id,$payload);
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
            $permission = $this->permissionRepository->delete($id);
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
            'id', 'name', 'canonical'
        ];
    }
}
