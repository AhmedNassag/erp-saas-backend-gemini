<?php

namespace Modules\Core\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface {
    public function all(array $columns = ['*'], array $relations = []): Collection;
    public function paginate(int $perPage = 15, array $relations = []): LengthAwarePaginator;
    public function find(int $id, array $relations = []): ?Model;
    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
