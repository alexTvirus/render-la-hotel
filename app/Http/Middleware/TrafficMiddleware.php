<?php

namespace App\Http\Middleware;

use App\Services\ComicServices;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrafficMiddleware
{

    public function handle($request, Closure $next)
    {
        $comicServices = app()->make(ComicServices::class);
        $comic_code = $request['comic_code'];

        if($comic_code){
            $entity = $comicServices->findByComicCode($comic_code);
            if($entity){
                ++$entity->total_view;
				$entity->save();
			}
        }

        return $next($request);
    }
}
