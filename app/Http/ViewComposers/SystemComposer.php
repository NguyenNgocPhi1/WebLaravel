<?php
namespace App\Http\ViewComposers;

use App\Repositories\Interfaces\SystemRepositoryInterface as SystemRepository;
use Illuminate\View\View;

class SystemComposer
{

    protected $systemRepository;
    protected $language;

    public function __construct(
        SystemRepository $systemRepository,
        $language
    ){
        $this->systemRepository = $systemRepository;
        $this->language = $language;
    }

    public function compose(View $view){
        $system = $this->systemRepository->findByCondition(
            [
                ['language_id', '=', $this->language]
            ],
            true
        );
        $systemArray = convert_array($system, 'keyword', 'content');
        $view->with('system', $systemArray);
    }
}