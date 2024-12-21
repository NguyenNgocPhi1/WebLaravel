<?php

namespace App\Http\Controllers\Backend\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\PostServiceInterface as PostService;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
// use App\Http\Requests\DeletePostRequest;
use App\Classes\Nestedsetbie;
use App\Models\Language;


class PostController extends Controller
{
    protected $postService;
    protected $postRepository;
    protected $language;
    
    public function __construct(PostService $postService,PostRepository $postRepository){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->postService = $postService;
        $this->postRepository = $postRepository;
        $this->initialize();
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => $this->language
        ]);
    }
        
    
    public function index(Request $request){
        $this->authorize('modules', 'post.index');
        $posts = $this->postService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Post'
        ];
        $config['seo'] = __('messages.post');
        $template = 'backend.post.post.index';
        $dropdown = $this->nestedset->Dropdown();
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'dropdown',
            'posts',
        ));
    }
    public function create(){ 
        $this->authorize('modules', 'post.create');
        $config = $this->configData();
        $config['seo'] = __('messages.post');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StorePostRequest $request){
        if($this->postService->create($request, $this->language)){
            return redirect()->route('post.index')->with('success','Thêm mới bản ghi thành công');    
        }
        return redirect()->route('post.index')->with('error','Thêm mới bản ghi thất bại. Hãy thử lại');    
    }

    public function edit($id){
        $this->authorize('modules', 'post.update');
        $posts = $this->postRepository->getPostById($id,$this->language);
        $config = $this->configData();
        $config['seo'] = __('messages.post');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout',compact(
            'template',
            'config',
            'posts',
            'dropdown',
        ));
    }

    public function update($id,UpdatePostRequest $request){
        if($this->postService->update($id,$request,$this->language)){
            return redirect()->route('post.index')->with('success','Cập nhật bản ghi thành công');    
        }
        return redirect()->route('post.index')->with('error','Cập nhật bản ghi thất bại. Hãy thử lại');    
    }

    public function delete($id){
        $this->authorize('modules', 'post.destroy');
        $posts = $this->postRepository->getPostById($id,$this->language);
        $config['seo'] = __('messages.post');
        $template = 'backend.post.post.delete';
        return view('backend.dashboard.layout',compact(
            'template',
            'posts',
            'config'
        ));
    }

    public function destroy($id){
        if($this->postService->destroy($id)){
            return redirect()->route('post.index')->with('success','Xóa bản ghi thành công');    
        }
        return redirect()->route('post.index')->with('error','Xóa bản ghi thất bại. Hãy thử lại');  
    }

    private function configData(){
        return [
            'js' => [
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
    }
}
