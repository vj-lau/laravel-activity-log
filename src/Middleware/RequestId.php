<?php

namespace VJLau\ActivityLog\Middleware\RequestId;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

/**
 * Created by PhpStorm.
 * User: VJLau
 * Date: 2017/2/16
 * Time: 下午8:25
 */
class RequestId
{
    /**
     * Add the Request ID header if needed.
     *
     * @param Request $request Request to be checked.
     * @param \Closure $next
     * @param null $guard
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, \Closure $next, $guard = null)
    {
        $uuid = $request->headers->get('X-Request-ID');
        if (is_null($uuid)) {
            $uuid = Uuid::uuid4()->toString();
            $request->headers->set('X-Request-ID', $uuid);
        }

        $response = $next($request);

        $response->headers->set('X-Request-ID', $uuid);

        return $response;
    }
}