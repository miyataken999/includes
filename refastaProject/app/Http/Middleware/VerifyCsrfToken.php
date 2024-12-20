<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'sagawa/shuukairai/',
        'rapa_diamond_price',
        'mailgun/receive_event/',
        'pushGoogleChatSpace/*',
        'api/realchat_count',
        //
    ];
}
