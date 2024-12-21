<?php

namespace App\Repositories;
use App\Repositories\Interfaces\WidgetRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Widget;

/**
 * Class WidgetService
 * @package App\Services
 */
class WidgetRepository extends BaseRepository implements WidgetRepositoryInterface
{
    protected $model;

    public function __construct(Widget $model){
        $this->model = $model;
    }
    
    public function getWidgetWhereIn(array $whereIn = [], $whereInField = 'keyword'){
        return $this->model->where(
            [config('apps.general.defaultPublish')],
        )
        ->whereIn($whereInField, $whereIn)
        ->orderByRaw("FIELD(keyword, '". implode("','", $whereIn) ."')")
        ->get();
    }
    
}
