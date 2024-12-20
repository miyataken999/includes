<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;

class CacheController extends Controller
{
    public function clearAllCache()
    {
        Cache::flush();
        return response()->json(['message' => 'cleared successfully']);
    }
}
