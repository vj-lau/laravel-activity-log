<?php
/**
 * Created by PhpStorm.
 * User: VJLau
 * Date: 2017/2/16
 * Time: 下午9:16
 */

namespace Bidzm\ActivityLog\Models;

use Bidzm\ActivityLog\Diff\Diff;

class ActivityLog extends BaseMongoLog
{
    /**
     * Get all of the owning loggable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function loggable()
    {
        return $this->morphTo();
    }

    /**
     * Get the diff for the activity.
     *
     * @return array
     */
    public function getDiffAttribute()
    {
        return Diff::make($this);
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        $this->casts['before'] = 'array';
        $this->casts['after'] = 'array';

        return parent::getCasts();
    }
}
