<?php

namespace App\Http\Controllers;

use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BaseController extends ResponseController
{
    protected BaseService $baseService;

    public function __construct(BaseService $baseService)
    {
        $this->baseService = $baseService;
    }


    
    public function index(Request $request): JsonResponse
    {
        try {
            $with = $request->input('with', []);
            $withCount = $request->input('withCount', []);
            $items = $this->baseService->get($request, $with, $withCount);
            return $this->ok($items);
        } catch (\Exception $e) {
            return $this->error([$e->getMessage()]);
        }
    }



    public function store(Request $request): JsonResponse
    {
        try {
            $item = $this->baseService->create($request);
            return $this->success($item, 'تم الحفظ بنجاح');
        } catch (ValidationException $e) {
            return $this->error($e->errors(), 422); // إرجاع أخطاء الفلترة للـ Vue 3
        } catch (\Exception $e) {
            return $this->error([$e->getMessage()]);
        }
    }



    public function show($id): JsonResponse
    {
        try {
            $with = request()->input('with', []);
            $item = $this->baseService->show($id, $with);
            return $this->ok($item);
        } catch (\Exception $e) {
            return $this->error(['السجل غير موجود'], 404);
        }
    }



    public function update($id, Request $request): JsonResponse
    {
        try {
            $item = $this->baseService->update($id, $request);
            return $this->success($item, 'تم التحديث بنجاح');
        } catch (ValidationException $e) {
            return $this->error($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error([$e->getMessage()]);
        }
    }



    public function destroy($id): JsonResponse
    {
        try {
            $this->baseService->delete($id);
            return $this->success(null, 'تم الحذف بنجاح');
        } catch (\Exception $e) {
            return $this->error([$e->getMessage()]);
        }
    }
}