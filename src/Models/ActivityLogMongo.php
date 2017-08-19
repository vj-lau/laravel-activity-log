<?php

namespace Bidzm\ActivityLog\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Bidzm\ActivityLog\Traits\ActivityLoggableTrait;

class ActivityLogMongo extends Model
{
    use ActivityLoggableTrait;

    protected $connection = 'mongodb';
}
