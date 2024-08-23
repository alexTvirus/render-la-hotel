<?php

namespace App\Http\Middleware;

use App\Services\ComicServices;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertIdMiddleware
{
//    private $keyArray = ['9', '1', '6', '4', '0', '8', '3', '2', '5', '7'];

    public function handle($request, Closure $next)
    {
        $id = $request->route('comic_code');
        if($id){
            $reversedString = reverseConvert($id, config('settings.arrray_keys_convert_id'));
            $request->offsetSet('comic_code', 'COMIC-'.$reversedString);
        }

        return $next($request);
    }
}
