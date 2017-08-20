<?php

namespace Bidzm\ActivityLog\Models;

use Bidzm\ActivityLog\Traits\ActivityLoggableTrait;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class ActivityLogEloquent extends Model
{
    use ActivityLoggableTrait,
        HybridRelations;

    protected $table = 'activity_logs';
}
