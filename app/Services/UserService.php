<?php

namespace App\Services;

use App\Services\Interfaces\UserServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Services\BaseService;

/**
 * Class UserService
 * @package App\Services
 */
class UserService extends BaseService implements UserServiceInterface
{

    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }
    
    public function paginate($request){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
            'user_catalogue_id' => $request->integer('user_catalogue_id'),
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = ['path' => 'user/index']; 

        $users = $this->userRepository->userPagination(
            $column,
            $condition, 
            $perpage,
            $extend, 
        );
        return $users;
    }
    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send','re_password']);
            if($payload['birthday'] != null){
                $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            }
            $payload['password'] = Hash::make($payload['password']);
            $user = $this->userRepository->create($payload);
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
            $user = $this->userRepository->update($id,$payload);
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
            $user = $this->userRepository->delete($id);
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
            'id', 'name', 'email', 'phone', 'address', 'publish', 'user_catalogue_id', 'image'
        ];
    }
}
