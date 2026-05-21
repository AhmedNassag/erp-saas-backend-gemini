<?php

namespace Modules\Core\Http\Controllers;

use Modules\Core\Services\BaseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseApiController extends Controller {
    protected BaseService $service;
    protected string $resource;
    protected string $storeRequest;
    protected string $updateRequest;
    protected array $relations = [];
    public function __construct(BaseService $service) { $this->service = $service; }
    public function index(Request $request): JsonResponse { $perPage = $request->get('per_page', 15); $data = $this->service->index(true, $perPage, $this->relations); return response()->json($this->resource::collection($data)->response()->getData(true), 200); }
    public function store(Request $request): JsonResponse { $validatedData = app($this->storeRequest)->validated(); $model = $this->service->store($validatedData); return response()->json(['message' => 'Created successfully', 'data' => new $this->resource($model)], 201); }
    public function show(int $id): JsonResponse { $model = $this->service->show($id, $this->relations); return response()->json(['data' => new $this->resource($model)], 200); }
    public function update(Request $request, int $id): JsonResponse { $validatedData = app($this->updateRequest)->validated(); $this->service->update($id, $validatedData); return response()->json(['message' => 'Updated successfully'], 200); }
    public function destroy(int $id): JsonResponse { $this->service->destroy($id); return response()->json(['message' => 'Deleted successfully'], 200); }
}
