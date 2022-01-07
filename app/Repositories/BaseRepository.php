<?php
declare(strict_types=1);


namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements EloquentRepositoryInterface
{
    /**
     * BaseRepository constructor.
     */
    public function __construct(protected Model $model) { }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function find($id): ?Model
    {
        return $this->model->find($id);
    }
}
