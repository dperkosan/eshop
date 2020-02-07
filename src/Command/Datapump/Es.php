<?php

namespace App\Command\Datapump;

class Es 
{
    private static $esUrl = "localhost";
    private static $esPort = "9200";

    public static function qryES($method, $endPoint, $qry=''){
        $ch = curl_init();
        $url = self::$esUrl.":".self::$esPort."/".$endPoint;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, self::$esPort);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpcode;
    }
}