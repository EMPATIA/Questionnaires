<?php
/**
 * Created by PhpStorm.
 * User: Vitor Fonseca
 * Date: 08/10/2015
 * Time: 15:34
 */

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Support\Facades\Cache;
use Request;
use Session;
use ONE;
use App\ComModules\TrackingRegistror;

class OneAuth
{
    /**
     * Create a new filter instance.
     *
     * @internal param Guard $auth
     */
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $validation = Cache::get($request->header('X-MODULE-TOKEN'));
        if(empty($validation)){
            $response = one::get(
                [
                    "component" => "orchestrator",
                    "api"       => "module",
                    "method"    => "checkToken",
                    "headers"   => ["X-MODULE-TOKEN: ". $request->header('X-MODULE-TOKEN')]
                ]
            );

            if($response->statuscode() == 200 || $response->statuscode() == 401){
                Cache::put($request->header('X-MODULE-TOKEN') , $response->json(), 60);
                if ( $response->statuscode() == 401){
                    return response()->json(['error' => 'Unauthorized Module'], 401)->send();
                }
            }
            else{
                return response()->json(['error' => 'Failed to verify Module Authorization'], 500)->send();
            }
        }elseif($validation === 'false'){
            return response()->json(['error' => 'Unauthorized, Module blocked'], 401)->send();
        }

        if($request->header('PERFORMANCE')=='1') {

            $methodRequest = $request->method();
            $urlRequest = $request->url();
            $result = app('Illuminate\Http\Response')->status();
            $moduleToken = env('MODULE_TOKEN');
            $trackingTableKey = TrackingRegistror::getLastTrackingKey();
            $time_start = microtime(true);

            TrackingRegistror::saveTrackingRequestsDataToDB($trackingTableKey, $urlRequest, $moduleToken, $methodRequest, $result, $time_start);
        }

        return $next($request);
    }

    public function terminate($request){

        if($request->header('PERFORMANCE')=='1'){

            $time_end = microtime(true);

            $response = One::Post([
                
                'component' => 'logs',
                'api' => 'TrackingController',
                'method' => 'updateTrackingRequestDataToDB',
                'params' => ["time_end"=> $time_end]
            ]);
        }


    }
}