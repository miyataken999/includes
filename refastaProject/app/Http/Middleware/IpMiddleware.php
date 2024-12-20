<?php

namespace App\Http\Middleware;

use Closure;

class IpMiddleware
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
      // IPの配列 * 実際に使う時はDBからデータを取得すると思います
      $ip = [
          ['id' => 1, 'name' => 'EVAさくらサーバー', 'ip' => '133.242.11.164'],
          ['id' => 2, 'name' => 'lounge VPN', 'ip' => '153.127.26.171'],
          ['id' => 2, 'name' => 'local', 'ip' => env("LOCALACCESS_ID", '172.18.0.1')],
          ['id' => 3, 'name' => 'ラウンジ本社', 'ip' => '124.219.164.138'],
          ['id' => 4, 'name' => 'izanagi', 'ip' => '35.189.145.255'],
          ['id' => 5, 'name' => '田宮家', 'ip' => '118.241.118.223'],
          ['id' => 6, 'name' => 'nishitani VPN', 'ip' => '160.16.203.204'],
          ['id' => 6, 'name' => 'kinkai server', 'ip' => '34.85.39.220'],
        ];
      /* 上の変数$ipにアクセスされたIPが含まれているかチェック */
      // $request->ip() で クライアント ipが取得できます
      $detect = collect($ip)->contains('ip', $request->ip());

      // ipが含まれていない時の処理
      if (!$detect) {
          // ここでは route()->name('invalid')にリダイレクト
          return redirect('invalid');
      }

      return $next($request);
    }
}
