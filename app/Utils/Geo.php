<?php

namespace Utils;

class Geo
{
    /**
     * Получает ip-адрес пользователя
     * @return false|mixed
     */
    public static function GetUserIP()
    {
        $ip_pattern = "/(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/";
        $search_keys = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];
        $local_ip_pattern = "/^(10|127|172|192\\.168)\\./";
        $ips = [];

        foreach ($search_keys as $k) {
            if (isset($_SERVER[$k]) && preg_match($ip_pattern, $_SERVER[$k], $_v)) {
                foreach ($_v as $__v) {
                    $ips[] = $__v;
                }
            }
        }

        do {
            $_v = array_shift($ips);
            if (!preg_match($local_ip_pattern, $_v)) $ip = $_v;
        } while (empty($ip) && count($ips) > 0);

        return $ip ?? false;
    }

    /**
     * Получает местоположение пользователя (страна, регион, город, координаты)
     * @param $ip
     * @return array|false
     */
    public static function GetLocationFromIP($ip)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address?ip={$ip}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Authorization: Token 9e781439fcd68346b5d6b58abe37810e654ad54e"
            ],
        ]);

        $response = json_decode(curl_exec($curl), true);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($status !== 200 || empty($response['location']) || empty($response['location']['data'])) return false;

        return [
            'country' => $response['location']['data']['country'],
            'region' => $response['location']['data']['region_with_type'],
            'city' => $response['location']['data']['city'],
            'lat' => $response['location']['data']['geo_lat'],
            'lng' => $response['location']['data']['geo_lon']
        ];
    }
}
