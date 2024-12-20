<?php

namespace App\Http\Middleware;

use Closure;

class AddResponseHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);


        if(
          isset($_SERVER["HTTP_X_FORWARDED_HOST"])
          &&
          (
            ($_SERVER["HTTP_X_FORWARDED_HOST"] == 'kinkaimasu.jp')
            OR ($_SERVER["HTTP_X_FORWARDED_HOST"] == 'brandkaimasu.com')
            OR ($_SERVER["HTTP_X_FORWARDED_HOST"] == 'diakaimasu.jp')
            OR ($_SERVER["HTTP_X_FORWARDED_HOST"] == 'rifa.life')
          )
        ){
          if($_SERVER["HTTP_X_FORWARDED_HOST"] == 'kinkaimasu.jp'){
            $allow = "https://kinkaimasu.jp";
            $response->headers->set('Access-Control-Allow-Origin', $allow);
          }elseif($_SERVER["HTTP_X_FORWARDED_HOST"] == 'brandkaimasu.com'){
            $allow = "https://brandkaimasu.com";
            $response->headers->set('Access-Control-Allow-Origin', $allow);
          }elseif($_SERVER["HTTP_X_FORWARDED_HOST"] == 'diakaimasu.jp'){
            $allow = "https://diakaimasu.jp";
            $response->headers->set('Access-Control-Allow-Origin', $allow);
          }elseif($_SERVER["HTTP_X_FORWARDED_HOST"] == 'rifa.life'){
            $allow = "https://rifa.life";
            $response->headers->set('Access-Control-Allow-Origin', $allow);
          }

          $response->headers->set('Access-Control-Allow-Headers', '*');
          $response->headers->set('Access-Control-Allow-Methods', 'GET');
        }else{
            if(
                isset($_SERVER["HTTP_REFERER"])
            ){
                if(strstr($_SERVER["HTTP_REFERER"], 'kinkaimasu.jp')){
                    $allow = "https://kinkaimasu.jp";
                    $response->headers->set('Access-Control-Allow-Origin', $allow);
                }elseif(strstr($_SERVER["HTTP_REFERER"], 'brandkaimasu.com')){
                    $allow = "https://brandkaimasu.com";
                    $response->headers->set('Access-Control-Allow-Origin', $allow);
                }elseif(strstr($_SERVER["HTTP_REFERER"], 'diakaimasu.jp')){
                    $allow = "https://diakaimasu.jp";
                    $response->headers->set('Access-Control-Allow-Origin', $allow);
                }elseif(strstr($_SERVER["HTTP_REFERER"], 'rifa.life')){
                    $allow = "https://rifa.life";
                    $response->headers->set('Access-Control-Allow-Origin', $allow);
                }

                $response->headers->set('Access-Control-Allow-Headers', '*');
                $response->headers->set('Access-Control-Allow-Methods', 'GET');
            }
        }

        if($request->ajax()){
          // If request from AJAX
          return [
              '_token' => Session::token(),
          ];
        }
        $response->headers->set('X-Frame-Options', 'sameorigin');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', '31536000');


        return $response;
    }
}
