<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use App\Traits\ImportDataTrait;
use App\Traits\SendNotificationDataTrait;
use Illuminate\Http\Request;

class BaseService
{
    use ImportDataTrait, SendNotificationDataTrait;

    protected BaseRepository $repository;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 🔐 ميثود الفحص الإجبارية: يتم إعادة كتابتها (Override) في الابن لتحديد الـ Rules
     * وتضمن عدم تمرير أي حقول خبيثة (تمنع الـ Mass Assignment العشوائي)
     */
    protected function validateData(Request $request, string $type = 'store'): array
    {
        return $request->all(); // الافتراضي، ويفضل تخصيصه في الابن
    }

    public function get(Request $request, array $with = [], array $withCount = [])
    {
        return $this->repository->get($request, $with, $withCount);
    }

    public function show($id, array $with = [])
    {
        return $this->repository->show($id, $with);
    }

    public function create(Request $request)
    {
        // 1. فرز وفحص البيانات أولاً بناءً على شروط الابن
        $validatedData = $this->validateData($request, 'store');
        
        // 2. لوجيك رفع الملفات الأوتوماتيكي اللي في ملفك
        if (method_exists($this, 'uploadFiles')) {
            $validatedData = $this->uploadFiles($validatedData);
        }

        return $this->repository->create($validatedData);
    }

    public function update($id, Request $request)
    {
        $validatedData = $this->validateData($request, 'update');
        
        if (method_exists($this, 'uploadFiles')) {
            $validatedData = $this->uploadFiles($validatedData);
        }

        return $this->repository->update($id, $validatedData);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}