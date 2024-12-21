<?php

namespace App\Services;

use App\Services\Interfaces\GenerateServiceInterface;
use App\Repositories\Interfaces\GenerateRepositoryInterface as GenerateRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;


/**
 * Class GenerateService
 * @package App\Services
 */
class GenerateService implements GenerateServiceInterface
{
    protected $generateRepository;
    
    public function __construct(GenerateRepository $generateRepository){
        $this->generateRepository = $generateRepository;
    }
    public function paginate($request){
        $column = $this->paginateSelect();
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
        ]; 
        $perpage = $request->integer('perpage'); 
        $extend = [
            'path' => 'generate/index', 
        ]; 
        
        $generate = $this->generateRepository->pagination(
            $column,
            $condition, 
            $perpage, 
            $extend, 
        );
        return $generate;
    }
    public function create($request){
        // DB::beginTransaction();
        try{

            $database = $this->makeDatabase($request);
            $controller = $this->makeController($request);
            $model = $this->makeModel($request);
            $repository = $this->makeRepository($request);
            $service = $this->makeService($request);
            $provider = $this->makeProvider($request);
            $makeRequest = $this->makeRequest($request);
            $View = $this->makeView($request);
            if($request->input('module_type') == 'catalogue'){
                $rule = $this->makeRule($request);
            }
            $route = $this->makeRoute($request);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }
    
    private function makeDatabase($request){
        try{
            $payload = $request->only('schema', 'name', 'module_type');
            $module = $this->converModuleNameToTableName($payload['name']);
            $moduleExtract = explode('_', $module);
            $this->makeMainTable($request, $module, $payload);
            if($payload['module_type'] !== 'defference'){
                $this->makeLanguageTable($request, $module);
                if(count($moduleExtract) == 1){
                    $this->makeRelationTable($request, $module);
                }
            }
            ARTISAN::call('migrate');
            return true;
        }catch(\Exception $e ){
            DB::rollback();
            echo $e->getMessage();die();
            return false;
        }
    }

    private function makeMainTable($request, $module, $payload){
        $moduleExtract = explode('_', $module);
        $tableName = $module.'s';
        $migrationFileName = date('Y_m_d_His').'_create_'.$tableName.'_table.php';
        $migrationPath = database_path('migrations/'.$migrationFileName);
        $migrationTemplate = $this->createMigrationFile($payload['schema'], $tableName);
        FILE::put($migrationPath, $migrationTemplate);
    }

    private function makeLanguageTable($request, $module){
        $foreignKey = $module.'_id';
        $pivotTableName = $module.'_language';
        $pivotSchema = $this->pivotSchema($module);
        $dropPivotTable = $module.'_language';
        $migrationPivotTemplate = $this->createMigrationFile($pivotSchema, $dropPivotTable);
        $migrationPivotFileName = date('Y_m_d_His', time() + 10).'_create_'.$pivotTableName.'_table.php';
        $migrationPivotPath = database_path('migrations/'.$migrationPivotFileName);
        FILE::put($migrationPivotPath, $migrationPivotTemplate);
    }

    private function makeRelationTable($request, $module){
        $moduleExtract = explode('_', $module);
        $tableName = $module.'_catalogue_'.$moduleExtract[0];
        $relationSchema = $this->relationSchema($tableName, $module);
        $migrationRelationTemplate = $this->createMigrationFile($relationSchema, $tableName);
        $migrationRelationFileName = date('Y_m_d_His', time() + 10).'_create_'.$tableName.'_table.php';
        $migrationRelationPath = database_path('migrations/'.$migrationRelationFileName);
        FILE::put($migrationRelationPath, $migrationRelationTemplate);
    }

    private function relationSchema($table = '',$module = ''){
        $schema = <<<SCHEMA
Schema::create('$table', function (Blueprint \$table) {
    \$table->unsignedBigInteger('{$module}_catalogue_id');
    \$table->unsignedBigInteger('{$module}_id');
    \$table->foreign('{$module}_catalogue_id')->references('id')->on('{$module}_catalogues')->onDelete('cascade');
    \$table->foreign('{$module}_id')->references('id')->on('{$module}s')->onDelete('cascade');
});
SCHEMA;
        return $schema;
    }

    private function pivotSchema($module){
        $pivotSchema = <<<SCHEMA
Schema::create('{$module}_language', function (Blueprint \$table) {
    \$table->unsignedBigInteger('{$module}_id');
    \$table->unsignedBigInteger('language_id');
    \$table->foreign('{$module}_id')->references('id')->on('{$module}s')->onDelete('cascade');
    \$table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
    \$table->string('name');
    \$table->text('description')->nullable();
    \$table->longText('content')->nullable();
    \$table->string('meta_title')->nullable();
    \$table->string('meta_keyword')->nullable();
    \$table->text('meta_description')->nullable();
    \$table->string('canonical')->nullable();
    \$table->timestamps();
});
SCHEMA;
        return $pivotSchema;
    }

    private function createMigrationFile($schema, $dropTable = ''){
        $migrationTemplate = <<<MIGRATION
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        {$schema}
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('{$dropTable}');
    }
};
MIGRATION; //PHP Heredoc
        return $migrationTemplate;
    }

    private function converModuleNameToTableName($name){
        $temp = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        return $temp;
    }

    private function makeController($request){
        $payload = $request->only('name','module_type');
        switch ($payload['module_type']) {
            case 'catalogue':
                $this->createTemplateCatalogueController($payload['name'], 'PostCatalogueController');
                break;
            case 'detail':
                $this->createTemplateCatalogueController($payload['name'], 'PostController');
                break;
            default:
                $this->createSingleController();
        }
    }

    private function createTemplateCatalogueController($name, $controllerFile){
        $controllerName = $name.'Controller.php';
        $templateControllerPath = base_path('app/Templates/controllers/'.$controllerFile.'.php');
        $module = explode('_', $this->converModuleNameToTableName($name));
        $controllerContent = file_get_contents($templateControllerPath);
        $replace = [
            '$class' => ucfirst(current($module)),
            'module' => lcfirst(current($module)),
        ];
        $newContent = $this->replaceContent($controllerContent, $replace);
        $controllerPath = base_path('app/Http/Controllers/Backend/'.$controllerName);
        FILE::put($controllerPath, $newContent);
    }

    private function makeModel($request){
        $moduleType = $request->input('module_type');
        $modelName = $request->input('name').'.php';
        switch ($moduleType) {
            case 'catalogue':
                $this->createCatalogueModel($request, $modelName);
                break;
            case 'detail':
                $this->createModel($request, $modelName);
                break;
            default:
                echo 1;die();
        }
    }

    private function createModel($request, $modelName){
        $template = base_path('app/Templates/models/Post.php');
        $content = file_get_contents($template);
        $module = $this->converModuleNameToTableName($request->input('name'));
        $replacement = [
            '$class' => ucfirst($module),
            '$module' => $module
        ];
        $newContent = $this->replaceContent($content, $replacement);
        $this->createModelFile($modelName, $newContent);
    }

    private function createCatalogueModel($request, $modelName){
        $templateModelPath = base_path('app/Templates/models/PostCatalogue.php');
        $modelContent = file_get_contents($templateModelPath);
        $module = $this->converModuleNameToTableName($request->input('name'));
        $extracModule = explode('_',$module);
        $replace = [
            '$class' => ucfirst($extracModule[0]),
            '$module' => $extracModule[0]
        ];
        
        foreach ($replace as $key => $val) {
            $modelContent = str_replace('{'.$key.'}', $replace[$key], $modelContent);
        }
        $this->createModelFile($modelName, $modelContent);
    }

    private function replaceContent($content, $replacement){
        $newContent = $content;
        foreach ($replacement as $key => $val) {
            $newContent = str_replace('{'.$key.'}', $replacement[$key], $newContent);
        }
        return $newContent;
    }

    private function createModelFile($modelName, $modelContent){
        $modelPath = base_path('app/Models/'.$modelName);
        FILE::put($modelPath, $modelContent);
    }

    private function makeRepository($request){
        $name = $request->input('name');
        $module = explode('_', $this->converModuleNameToTableName($name));
        $repositoryPath = (count($module) == 1) ? base_path('app/Templates/repositories/PostRepository.php') : base_path('app/Templates/repositories/PostCatalogueRepository.php');
        $path = [
            'Interfaces' => base_path('app/Templates/repositories/TemplateRepositoryInterface.php'),
            'Repositories' => $repositoryPath
        ];
        $replacement = [
            '$class' => ucfirst(current($module)),
            'module' => lcfirst(current($module)),
            '$extend' => (count($module) == 2) ? 'Catalogue' : ''
        ];
        foreach ($path as $key => $val) {
            $content = file_get_contents($val);
            $newContent = $this->replaceContent($content, $replacement);
            $contentPath = ($key == 'Interfaces') ? base_path('app/Repositories/Interfaces/'.$name.'RepositoryInterface.php') : base_path('app/Repositories/'.$name.'Repository.php');
            if(!File::exists($contentPath)){
                FILE::put($contentPath, $newContent);
            }
        }
    }

    private function makeService($request){
        $name = $request->input('name');
        $module = explode('_', $this->converModuleNameToTableName($name));
        $servicePath = (count($module) == 1) ? base_path('app/Templates/services/PostService.php') : base_path('app/Templates/services/PostCatalogueService.php');
        $path = [
            'Interfaces' => base_path('app/Templates/services/TemplateServiceInterface.php'),
            'Services' => $servicePath
        ];
        $replacement = [
            '$class' => ucfirst(current($module)),
            'module' => lcfirst(current($module)),
            '$extend' => (count($module) == 2) ? 'Catalogue' : ''
        ];
        foreach ($path as $key => $val) {
            $content = file_get_contents($val);
            $newContent = $this->replaceContent($content, $replacement);
            $contentPath = ($key == 'Interfaces') ? base_path('app/Services/Interfaces/'.$name.'ServiceInterface.php') : base_path('app/Services/'.$name.'Service.php');
            if(!File::exists($contentPath)){
                FILE::put($contentPath, $newContent);
            }
        }
    }

    private function makeProvider($request){
        $name = $request->input('name');
        $provider = [
            'providerPath' => base_path('app/Providers/AppServiceProvider.php'),
            'repositoryProviderPath' => base_path('app/Providers/RepositoryServiceProvider.php')
        ];
        foreach ($provider as $key => $val) {
            $content = file_get_contents($val);
            $insertLine = ($key == 'providerPath') ? "'App\\Services\\Interfaces\\{$name}ServiceInterface' =>
        'App\\Services\\{$name}Service'," : "'App\\Repositories\\Interfaces\\{$name}RepositoryInterface' =>
        'App\\Repositories\\{$name}Repository',";
            $position = strpos($content,'];');
            if($position !== false){
                $newContent = substr_replace($content,"    ".$insertLine."\n"."    ",$position,0);
            }
            File::put($val,$newContent);
        }
    }

    private function makeRequest($request){
        $name = $request->input('name');
        $requestArray = ['Store'.$name.'Request','Update'.$name.'Request','Delete'.$name.'Request'];
        $requestTemplate = ['RequestTemplateStore','RequestTemplateUpdate','RequestTemplateDelete'];
        if($request->input('module_type') != 'catalogue'){
            unset($requestArray[2]);
            unset($requestTemplate[2]);
        }

        foreach ($requestTemplate as $key => $val) {
            $requestPath = base_path('app/Templates/requests/'.$val.'.php');
            $requestContent = file_get_contents($requestPath);
            $requestContent = str_replace('{Module}', $name, $requestContent);
            $requestPut = base_path('app/Http/Requests/'.$requestArray[$key].'.php');
            File::put($requestPut, $requestContent);
        }
    }

    private function makeView($request){
        try{
            $name = $request->input('name');
            $module = $this->converModuleNameToTableName($name);
            $extracModule = explode('_', $module);
            $basePath = resource_path("views/backend/{$extracModule[0]}");
            $folderPath = (count($extracModule) == 2) ? "$basePath/{$extracModule[1]}" : "$basePath/{$extracModule[0]}";
            $componentPath = "$folderPath/component";
            $this->createDirector($folderPath);
            $this->createDirector($componentPath);
            $sourcePath = base_path('app/Templates/views/'.((count($extracModule) == 2) ? 'catalogue' : 'post').'/');
            $viewPath = (count($extracModule) == 2) ? "{$extracModule[0]}.{$extracModule[1]}" : $extracModule[0];
            $replacement = [
                'view' => $viewPath,
                'module' => lcfirst($name),
                'Module' => $name
            ];
            $fileArray = ['store.blade.php','index.blade.php','delete.blade.php'];
            $componentFile = ['aside.blade.php','filter.blade.php','table.blade.php'];
            $this->copyAndReplaceContent($sourcePath, $folderPath, $fileArray, $replacement);
            $this->copyAndReplaceContent("{$sourcePath}component/", $componentPath, $componentFile, $replacement);
            return true;
        }catch(\Exception $e ){
            echo $e->getMessage();die();
            return false;
        }
    }

    private function createDirector($path){
        if(!File::exists($path)){
            File::makeDirectory($path, 0755, true);
        }
    }

    private function copyAndReplaceContent(string $sourcePath,string $destinationPath,array $fileArray,array $replacement){
        foreach ($fileArray as $key => $val) {
            $sourceFile = $sourcePath.$val;
            $destination = "{$destinationPath}/{$val}";
            $content = file_get_contents($sourceFile);
            foreach ($replacement as $keyReplace => $replace) {
                $content = str_replace('{'.$keyReplace.'}', $replace, $content);
            }
            if(!File::exists($destination)){
                File::put($destination, $content);
            }
        }
    }

    private function makeRule($request){
        try{
            $name = $request->input('name');
            $ruleName = 'Check'.$name.'ChildrenRule.php';
            $templateRulePath = base_path('app/Templates/RuleTemplate.php');
            $ruleContent = file_get_contents($templateRulePath);
            $ruleContent = str_replace('{Module}', $name, $ruleContent);          
            $rulePath = base_path('app/Rules/'.$ruleName);
            if(!File::exists($rulePath)){
                FILE::put($rulePath, $ruleContent);
            }
            return true;
        }catch(\Exception $e ){
            echo $e->getMessage();die();
            return false;
        }
    }

    private function makeRoute($request){
        $name = $request->input('name');
        $module = $this->converModuleNameToTableName($name);
        $moduleExtract = explode('_', $module);
        $routesPath = base_path('routes/web.php');
        $content = file_get_contents($routesPath);
        $routeUrl = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}/{$moduleExtract[1]}" : $moduleExtract[0];
        $routeName = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}.{$moduleExtract[1]}" : $moduleExtract[0];
        $routeGroup = <<<ROUTE
            Route::group(['prefix' => '$routeUrl'], function (){
                Route::get('index', [{$name}Controller::class, 'index'])->name('{$routeName}.index');
                Route::get('create', [{$name}Controller::class, 'create'])->name('{$routeName}.create');
                Route::post('store', [{$name}Controller::class, 'store'])->name('{$routeName}.store');
                Route::get('{id}/edit', [{$name}Controller::class, 'edit'])->where(['id' => '[0-9]+'])->name('{$routeName}.edit');
                Route::post('{id}/update', [{$name}Controller::class, 'update'])->where(['id' => '[0-9]+'])->name('{$routeName}.update');
                Route::get('{id}/delete', [{$name}Controller::class, 'delete'])->where(['id' => '[0-9]+'])->name('{$routeName}.delete');
                Route::delete('{id}/destroy', [{$name}Controller::class, 'destroy'])->where(['id' => '[0-9]+'])->name('{$routeName}.destroy');
            });

            //@new-module@@

        ROUTE;

        $userController = <<<ROUTE
            use App\Http\Controllers\Backend\\{$name}Controller;
            //@useController@@
        ROUTE;
        
        $content = str_replace('//@new-module@@', $routeGroup, $content);
        $content = str_replace('//@useController@@', $userController, $content);
        File::put($routesPath, $content);
    }

    public function update($id,$request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send']);
            $generate = $this->generateRepository->update($id,$payload);
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
            $generate = $this->generateRepository->delete($id);
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
            'id', 'name', 'schema'
        ];
    }
}
