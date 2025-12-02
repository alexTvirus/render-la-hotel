<?php


namespace App\Facade;

use Illuminate\Support\Facades\Facade;

class LaravelGmailExtend extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'laravelgmailextend';
	}
}
