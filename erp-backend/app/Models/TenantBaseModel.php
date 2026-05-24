<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantBaseModel extends Model
{
    // 💡 أي موديل يورث من هذا الكلاس سيقرأ أوتوماتيكياً من داتا بيز العميل الحالي
    protected $connection = 'tenant';
}