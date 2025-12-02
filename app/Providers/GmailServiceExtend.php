<?php

namespace App\Providers;

use Dacastro4\LaravelGmail\Services\Message\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Dacastro4\LaravelGmail\LaravelGmailClass;

class GmailServiceExtend extends LaravelGmailClass
{
    public function __construct($config, $userId = null)
	{
		if (class_basename($config) === 'Application') {
			$config = $config['config'];
		}

		parent::__construct($config, $userId);
	}

	public function refreshTokenExtend()
	{

			$token = $this->getAccessToken();
			$this->setBothAccessToken($token);

			return $token;
	}


}
