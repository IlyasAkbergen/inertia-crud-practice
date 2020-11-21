<?php


namespace App\Services;


use Illuminate\Database\Eloquent\Model;

interface BaseService
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection
    */
    public function all();

    /**
     * @param array $relationships
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWith(array $relationships);

    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * @param int $id
     * @param array $attributes
     * @return Model
     */
    public function update($id, array $attributes): Model;

    /**
     * @param int $id
     */
    public function delete($id);

    /**
     * @param $id
     * @return Model
     */
    public function find($id): ?Model;

    /**
     * @param array $params
     * @return Model
    */
    public function firstWhere(array $params);

    /**
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere(array $params);
}