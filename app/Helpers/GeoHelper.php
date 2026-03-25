<?php

namespace App\Helpers;

class GeoHelper
{
    public static function getClientIP() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipList[0]);
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    public static function getIPLocation($ip) {
        $apiKey = "365edb989ce04d3297d1c22a9ab41ef8";
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey={$apiKey}&ip={$ip}";

        $response = @file_get_contents($url);
        if (!$response) {
            return null;
        }

        return json_decode($response, true);
    }
}
