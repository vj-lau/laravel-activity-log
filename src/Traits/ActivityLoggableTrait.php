<?php

namespace Bidzm\ActivityLog\Traits;

use Bidzm\ActivityLog\Diff\Diff;

trait ActivityLoggableTrait
{
    public function actor()
    {
        return $this->morphTo();
    }

    public function loggable()
    {
        return $this->morphTo();
    }

    public function getDiffAttributes()
    {
        return Diff::make($this);
    }

    public function getCasts()
    {
        $this->casts['before'] = 'array';
        $this->casts['after'] = 'array';

        return parent::getCasts();
    }
}
