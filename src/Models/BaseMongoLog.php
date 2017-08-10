<?php
/**
 * Created by PhpStorm.
 * User: VJLau
 * Date: 2017/2/16
 * Time: 下午9:09
 */

namespace Bidzm\ActivityLog\Models;

use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class BaseMongoLog extends Moloquent
{
    protected $connection = 'mongodb';  //库名
}
