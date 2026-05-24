<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BaseRepository
{
    /**
     * Return a fresh instance of the model.
     */
    abstract public function getModel(): Model;

    /**
     * Get a paginated / filtered list of records.
     */
    public function get(Request $request, array $with = [], array $withCount = [])
    {
        $query = $this->getModel()->newQuery();

        if (! empty($with)) {
            $query->with($with);
        }

        if (! empty($withCount)) {
            $query->withCount($withCount);
        }

        $perPage = $request->input('per_page', config('myConfig.paginationCount', 15));

        return $perPage == -1 ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Find a single record by ID.
     */
    public function show($id, array $with = []): Model
    {
        $query = $this->getModel()->newQuery();

        if (! empty($with)) {
            $query->with($with);
        }

        return $query->findOrFail($id);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->getModel()->create($data);
    }

    /**
     * Update an existing record.
     */
    public function update($id, array $data): Model
    {
        $model = $this->getModel()->findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    /**
     * Delete a record.
     */
    public function delete($id): bool
    {
        return $this->getModel()->findOrFail($id)->delete();
    }
}
