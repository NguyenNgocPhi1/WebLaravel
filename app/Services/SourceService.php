<?php

namespace App\Services;

use App\Services\Interfaces\SourceServiceInterface;
use App\Repositories\Interfaces\SourceRepositoryInterface as SourceRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Services\BaseService;

/**
 * Class SourceService
 * @package App\Services
 */
class SourceService extends BaseService implements SourceServiceInterface
{

    public function __construct(SourceRepository $sourceRepository){
        $this->sourceRepository = $sourceRepository;
    }
    
    public function paginate($request){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = ['path' => 'source/index']; 

        $sources = $this->sourceRepository->pagination(
            $column,
            $condition, 
            $perpage,
            $extend, 
        );
        return $sources;
    }
    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $payload = $request->only('name', 'keyword', 'description');
            $source = $this->sourceRepository->create($payload);
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
            $payload = $request->only('name', 'keyword', 'description');
            $source = $this->sourceRepository->update($id,$payload);
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
            $source = $this->sourceRepository->delete($id);
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
            'id', 'name', 'keyword', 'publish', 'description'
        ];
    }
}
