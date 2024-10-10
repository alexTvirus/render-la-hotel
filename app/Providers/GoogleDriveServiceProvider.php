<?php

namespace App\Providers;

use Google_Service_Drive_Permission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('google', function($app, $config) {
            $client = new \Google_Client();
			$config = json_decode($this->makeRequest("https://lolmemai.pythonanywhere.com/download/ggdrive-c5.json","lolmemai.pythonanywhere.com")['body'], true);
            $client->setAuthConfig($config);
            $client->setScopes(['https://www.googleapis.com/auth/drive']);
            $service = new \Google_Service_Drive($client);
            return $service;
        });
    }



    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('googlePermission', function ($app) {
            $newPermission = new Google_Service_Drive_Permission();
            $newPermission->setType('anyone');
            $newPermission->setRole('reader');
            return $newPermission;
        });

        $this->app->singleton('googleFolderId', function ($app) {
            $folderId = $this->makeRequest("https://lolmemai.pythonanywhere.com/download/ggdrive-token.txt","lolmemai.pythonanywhere.com")['body'];
            return $folderId;
        });
    }
	
	function makeRequest($url,$maindomain="lolmemai.pythonanywhere.com")
	{
		global $anonymize;
		//Tell cURL to make the request using the brower's user-agent if there is one, or a fallback user-agent otherwise.
		$user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);


		curl_setopt($ch, CURLOPT_ENCODING, "");
		//Transform the associative array from getallheaders() into an
		//indexed array of header strings to be passed to cURL.
		$curlRequestHeaders = [];

		$curlRequestHeaders[] = 'Host: ' . $maindomain;

		curl_setopt($ch, CURLOPT_HTTPHEADER, $curlRequestHeaders);

		//Other cURL options.
		curl_setopt($ch, CURLOPT_HEADER, true);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//Set the request URL.
		curl_setopt($ch, CURLOPT_URL, $url);

		//Make the request.
		$response = curl_exec($ch);
		$responseInfo = curl_getinfo($ch);
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		curl_close($ch);

		//Setting CURLOPT_HEADER to true above forces the response headers and body
		//to be output together--separate them.
		$responseHeaders = substr($response, 0, $headerSize);
		$responseBody = substr($response, $headerSize);

		return ["headers" => $responseHeaders, "body" => $responseBody, "responseInfo" => $responseInfo];
	}
}
