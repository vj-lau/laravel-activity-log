<?php

namespace Bidzm\ActivityLog;

use Illuminate\Database\Eloquent\SoftDeletes;
use Bidzm\ActivityLog\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

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

        if (in_array(SoftDeletes::class, class_uses(static::class))) {
            static::restored(function ($model) {
                $model->logRestored();
            });
        }
    }

    /**
     * Log attributes for the "created" event.
     *
     * @return void
     */
    protected function logCreated()
    {
        $after = $this->getLoggableAttributes();

        $this->log(null, $after, 'created');
    }

    /**
     * Log attributes for the "updated" event.
     *
     * @return void
     */
    protected function logUpdated()
    {
        $after = $this->getLoggableAttributes();

        $before = array_intersect_key($this->getOriginal(), $after);

        if ($before != $after) {
            $this->log($before, $after, 'updated');
        }
    }

    /**
     * Log attributes for the "deleted" event.
     *
     * @return void
     */
    protected function logDeleted()
    {
        $before = $this->getLoggableAttributes();

        $this->log($before, null, 'deleted');
    }

    /**
     * Log attributes for the "restored" event.
     *
     * @return void
     */
    protected function logRestored()
    {
        $after = $this->getLoggableAttributes();

        $this->log(null, $after, 'restored');
    }

    /**
     * Get the model's loggable attributes.
     *
     * @return array
     */
    protected function getLoggableAttributes()
    {
        $except = property_exists($this, 'logExcept')
            ? $this->logExcept
            : config('activity-log.log.except');

        return array_diff_key(
            $this->getAttributes(), array_flip($except)
        );
    }

    /**
     * Save an activity log.
     *
     * @return void
     */
    public function log($before, $after, $event)
    {
        if ((empty($before) && empty($after)) || !auth()->check()) {
            return;
        }

        $request = request();
        $log = new ActivityLog;
        $log->request_id = $request->headers->get('X-Request-ID');
        $log->event = $event;
        $log->before = $before;
        $log->after = $after;
        $log->model = get_class($this);
        $log->save();
    }

    /**
     * Get the diffRaw attribute.
     *
     * @return mixed
     */
    public function diffRaw()
    {
        if (!property_exists($this, 'diffRaw')) {
            return config('activity-log.diff.raw');
        }

        return $this->diffRaw;
    }

    /**
     * Get the diffGranularity attribute.
     *
     * @return mixed
     */
    public function diffGranularity()
    {
        if (!property_exists($this, 'diffGranularity')) {
            return config('activity-log.diff.granularity');
        }

        return $this->diffGranularity;
    }
}
