<?php

namespace App\Repositories;

use CodeIgniter\Model;

abstract class BaseRepository
{
    protected Model $model;
    
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
    public function find($id)
    {
        return $this->model->find($id);
    }
    
    public function findAll(int $limit = 0, int $offset = 0)
    {
        return $this->model->findAll($limit, $offset);
    }
    
    public function findBy(array $conditions, array $select = ['*'])
    {
        $builder = $this->model->select($select);
        foreach ($conditions as $field => $value) {
            $builder->where($field, $value);
        }
        return $builder->findAll();
    }
    
    public function findOneBy(array $conditions)
    {
        $builder = $this->model;
        foreach ($conditions as $field => $value) {
            $builder->where($field, $value);
        }
        return $builder->first();
    }
    
    public function create(array $data)
    {
        return $this->model->insert($data);
    }
    
    public function update($id, array $data)
    {
        return $this->model->update($id, $data);
    }
    
    public function delete($id)
    {
        return $this->model->delete($id);
    }
}