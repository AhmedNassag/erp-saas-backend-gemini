<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

trait ActivityLogTrait
{
    use LogsActivity;

    protected static $logName;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::$logName = Str::snake(class_basename($this));
    }

    public function activityLogs()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected function getLogNameToUse()
    {
        return isset(static::$logName) ? static::$logName : config('activitylog.default_log_name');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} on " . Str::snake(class_basename($this));
    }

    protected function getModelName()
    {
        return class_basename($this);
    }
}
