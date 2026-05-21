<?php

namespace Modules\Core\Services;

use Modules\Core\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService {
    protected BaseRepositoryInterface $repository;
    public function __construct(BaseRepositoryInterface $repository) { $this->repository = $repository; }
    public function index(bool $needsPagination = true, int $perPage = 15, array $relations = []) { return $needsPagination ? $this->repository->paginate($perPage, $relations) : $this->repository->all(['*'], $relations); }
    public function show(int $id, array $relations = []): ?Model { return $this->repository->find($id, $relations); }
    public function store(array $data): Model { return $this->repository->create($data); }
    public function update(int $id, array $data): bool { return $this->repository->update($id, $data); }
    public function destroy(int $id): bool { return $this->repository->delete($id); }
}
