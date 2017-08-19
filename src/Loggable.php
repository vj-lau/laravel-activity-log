<?php

namespace Bidzm\ActivityLog;

use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\SoftDeletes as SoftDeleteMongo;
use Bidzm\ActivityLog\Models\ActivityLog;

trait Loggable
{
    /**
     * Boot loggable trait.
     *
     * @return void
     */
    public static function bootLoggable()
    {
        static::created(function ($model) {
            $model->logCreated();
        });

        static::updated(function ($model) {
            $model->logUpdated();
        });

        static::deleted(function ($model) {
            $model->logDeleted();
        });

        if (in_array(SoftDeletes::class, class_uses(static::class)) ||
            in_array(SoftDeleteMongo::class, class_uses(static::class))) {
            static::restored(function ($model) {
                $model->logRestored();
            });
        }
    }

    protected function logCreated()
    {
        $after = $this->getLoggableAttributes();

        $this->log(null, $after, 'created');
    }

    protected function logUpdated()
    {
        $after = $this->getLoggableAttributes();

        $before = array_intersect_key($this->getOriginal(), $after);

        if ($before != $after) {
            $this->log($before, $after, 'updated');
        }
    }

    protected function logDeleted()
    {
        $before = $this->getLoggableAttributes();

        $this->log($before, null, 'deleted');
    }

    protected function logRestored()
    {
        $after = $this->getLoggableAttributes();

        $this->log(null, $after, 'restored');
    }

    protected function getLoggableAttributes()
    {
        $except = property_exists($this, 'logExcept')
            ? $this->logExcept
            : config('activity-log.log.except');

        return array_diff_key(
            $this->getAttributes(), array_flip($except)
        );
    }

    public function logs()
    {
        return $this->morphMany('Bidzm\ActivityLog\Models\ActivityLog', 'loggable');
    }

    /**
     * Save an activity log.
     *
     * @return void
     */
    public function log($before, $after, $event)
    {
        if (empty($before) && empty($after)) {
            return;
        }

        $log = new ActivityLog;
        $log->actor = auth()->user();
        $log->event = $event;
        $log->before = $before;
        $log->after = $after;
        $log->loggable()->associate($this);
        $log->save();
    }

    public function diffRaw()
    {
        if (!property_exists($this, 'diffRaw')) {
            return config('activity-log.diff.raw');
        }

        return $this->diffRaw;
    }

    public function diffGranularity()
    {
        if (!property_exists($this, 'diffGranularity')) {
            return config('activity-log.diff.granularity');
        }

        return $this->diffGranularity;
    }
}
