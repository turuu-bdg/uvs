<?php


namespace App\Lib;


class ProcessUrl
{
    function processURL($url) {
        $url = str_replace('&amp;', '&', $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $xml = curl_exec($ch);

        $errno = curl_errno($ch);

        if ($errno != 0) {
            curl_close($ch);
            return "ERROR";
        }

        curl_close($ch);
        return $xml;
    }
}