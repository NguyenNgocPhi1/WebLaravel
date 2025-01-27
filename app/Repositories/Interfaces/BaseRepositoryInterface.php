<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function all(array $relation = []);
    public function findById(int $id);
    public function create(array $payload = []);
    public function update(int $id = 0, array $payload = []);
    public function delete(int $id = 0);
    public function pagination( array $column = ['*'], 
                                array $condition= [], 
                                int $perpage = 1, 
                                array $extend = [], 
                                array $orderBy = ['id','desc'],
                                array $join = [], 
                                array $relations = [],
                                array $rawQuery = []);
    public function updateWhereIn(string $whereInField = '', array $whereIn = [], array $payload = []);
    public function createPivot($model, array $payload = [],string $relation = '');
    public function forceDeleteByCondition(array $condition = []);
    public function createBatch(array $payload = []);
    public function updateOrInsert(array $payload = [], array $condition = []);
    public function findByCondition($condition = [], $flag = false, $relation = [],array $orderBy = ['id', 'desc'], array $param = [], array $withCount = []);
    public function findByWhereHas(array $condition = [], string $relation = '', string $alias = '');
}
