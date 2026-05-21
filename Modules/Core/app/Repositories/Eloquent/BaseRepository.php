<?php

namespace Modules\Core\Repositories\Eloquent;

use Modules\Core\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface {
    protected Model $model;
    public function __construct() { $this->model = app($this->getModelClass()); }
    abstract protected function getModelClass(): string;
    public function all(array $columns = ['*'], array $relations = []): Collection { return $this->model->with($relations)->get($columns); }
    public function paginate(int $perPage = 15, array $relations = []): LengthAwarePaginator { return $this->model->with($relations)->latest()->paginate($perPage); }
    public function find(int $id, array $relations = []): ?Model { return $this->model->with($relations)->findOrFail($id); }
    public function create(array $data): Model { return $this->model->create($data); }
    public function update(int $id, array $data): bool { return $this->find($id)->update($data); }
    public function delete(int $id): bool { return $this->find($id)->delete(); }
}
