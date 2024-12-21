<?php
namespace App\Http\ViewComposers;

use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use Illuminate\View\View;

class LanguageComposer
{

    // protected $languageRepository;

    public function __construct(
        LanguageRepository $languageRepository,
        $language
    ){
        $this->languageRepository = $languageRepository;
    }

    public function compose(View $view){
        $languages = $this->languageRepository->findByCondition(...$this->agrument());
        $view->with('languages', $languages);
    }

    private function agrument(){
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => [],
            'orderBy' => ['current', 'desc']
        ];
    }

}