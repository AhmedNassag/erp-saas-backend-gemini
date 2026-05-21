<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateBaseArchitectureCommand extends Command
{
    // الاسم اللي هنشغل بيه الأمر من الـ Terminal
    protected $signature = 'make:base-architecture';
    protected $description = 'Generate core base classes inside Core Module';

    public function handle()
    {
        // 1. إنشاء الـ BaseRepositoryInterface
        $p1 = base_path('Modules/Core/app/Repositories/Contracts/BaseRepositoryInterface.php');
        File::ensureDirectoryExists(dirname($p1));
        File::put($p1, "<?php\n\nnamespace Modules\Core\Repositories\Contracts;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Collection;\nuse Illuminate\Pagination\LengthAwarePaginator;\n\ninterface BaseRepositoryInterface {\n    public function all(array \$columns = ['*'], array \$relations = []): Collection;\n    public function paginate(int \$perPage = 15, array \$relations = []): LengthAwarePaginator;\n    public function find(int \$id, array \$relations = []): ?Model;\n    public function create(array \$data): Model;\n    public function update(int \$id, array \$data): bool;\n    public function delete(int \$id): bool;\n}\n");

        // 2. إنشاء الـ BaseRepository
        $p2 = base_path('Modules/Core/app/Repositories/Eloquent/BaseRepository.php');
        File::ensureDirectoryExists(dirname($p2));
        File::put($p2, "<?php\n\nnamespace Modules\Core\Repositories\Eloquent;\n\nuse Modules\Core\Repositories\Contracts\BaseRepositoryInterface;\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Collection;\nuse Illuminate\Pagination\LengthAwarePaginator;\n\nabstract class BaseRepository implements BaseRepositoryInterface {\n    protected Model \$model;\n    public function __construct() { \$this->model = app(\$this->getModelClass()); }\n    abstract protected function getModelClass(): string;\n    public function all(array \$columns = ['*'], array \$relations = []): Collection { return \$this->model->with(\$relations)->get(\$columns); }\n    public function paginate(int \$perPage = 15, array \$relations = []): LengthAwarePaginator { return \$this->model->with(\$relations)->latest()->paginate(\$perPage); }\n    public function find(int \$id, array \$relations = []): ?Model { return \$this->model->with(\$relations)->findOrFail(\$id); }\n    public function create(array \$data): Model { return \$this->model->create(\$data); }\n    public function update(int \$id, array \$data): bool { return \$this->find(\$id)->update(\$data); }\n    public function delete(int \$id): bool { return \$this->find(\$id)->delete(); }\n}\n");

        // 3. إنشاء الـ BaseService
        $p3 = base_path('Modules/Core/app/Services/BaseService.php');
        File::ensureDirectoryExists(dirname($p3));
        File::put($p3, "<?php\n\nnamespace Modules\Core\Services;\n\nuse Modules\Core\Repositories\Contracts\BaseRepositoryInterface;\nuse Illuminate\Database\Eloquent\Model;\n\nabstract class BaseService {\n    protected BaseRepositoryInterface \$repository;\n    public function __construct(BaseRepositoryInterface \$repository) { \$this->repository = \$repository; }\n    public function index(bool \$needsPagination = true, int \$perPage = 15, array \$relations = []) { return \$needsPagination ? \$this->repository->paginate(\$perPage, \$relations) : \$this->repository->all(['*'], \$relations); }\n    public function show(int \$id, array \$relations = []): ?Model { return \$this->repository->find(\$id, \$relations); }\n    public function store(array \$data): Model { return \$this->repository->create(\$data); }\n    public function update(int \$id, array \$data): bool { return \$this->repository->update(\$id, \$data); }\n    public function destroy(int \$id): bool { return \$this->repository->delete(\$id); }\n}\n");

        // 4. إنشاء الـ BaseApiController
        $p4 = base_path('Modules/Core/app/Http/Controllers/BaseApiController.php');
        File::ensureDirectoryExists(dirname($p4));
        File::put($p4, "<?php\n\nnamespace Modules\Core\Http\Controllers;\n\nuse Modules\Core\Services\BaseService;\nuse App\Http\Controllers\Controller;\nuse Illuminate\Http\JsonResponse;\nuse Illuminate\Http\Request;\n\nabstract class BaseApiController extends Controller {\n    protected BaseService \$service;\n    protected string \$resource;\n    protected string \$storeRequest;\n    protected string \$updateRequest;\n    protected array \$relations = [];\n    public function __construct(BaseService \$service) { \$this->service = \$service; }\n    public function index(Request \$request): JsonResponse { \$perPage = \$request->get('per_page', 15); \$data = \$this->service->index(true, \$perPage, \$this->relations); return response()->json(\$this->resource::collection(\$data)->response()->getData(true), 200); }\n    public function store(Request \$request): JsonResponse { \$validatedData = app(\$this->storeRequest)->validated(); \$model = \$this->service->store(\$validatedData); return response()->json(['message' => 'Created successfully', 'data' => new \$this->resource(\$model)], 201); }\n    public function show(int \$id): JsonResponse { \$model = \$this->service->show(\$id, \$this->relations); return response()->json(['data' => new \$this->resource(\$model)], 200); }\n    public function update(Request \$request, int \$id): JsonResponse { \$validatedData = app(\$this->updateRequest)->validated(); \$this->service->update(\$id, \$validatedData); return response()->json(['message' => 'Updated successfully'], 200); }\n    public function destroy(int \$id): JsonResponse { \$this->service->destroy(\$id); return response()->json(['message' => 'Deleted successfully'], 200); }\n}\n");

        $this->info('Base Architecture generated successfully inside Modules/Core/app!');
    }
}