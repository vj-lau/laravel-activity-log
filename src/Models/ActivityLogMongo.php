<?php

namespace Bidzm\ActivityLog\Models;

use Bidzm\ActivityLog\Traits\ActivityLoggableTrait;
use Jenssegers\Mongodb\Eloquent\Model;

class ActivityLogMongo extends Model
{
    use ActivityLoggableTrait;

    protected $connection = 'mongodb';
    protected $collection = 'activity_logs';
}
