<?php

function actions_post($url, $headers, $body)
{
    $headers[] = "Content-Type: application/json";
    $postBody = json_encode($body);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);//设置请求头
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postBody);//设置请求体
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');//使用一个自定义的请求信息来代替"GET"或"HEAD"作为HTTP请求。(这个加不加没啥影响)
    $data = curl_exec($curl);
    return $data;
}


function actions_get($url, $headers, $body)
{
    $headers[] = "Content-Type: application/json";

    if (!empty($body)) {
        $queryString = http_build_query($body);
        $separator = strpos($url, '?') !== false ? '&' : '?';
        $url .= $separator . $queryString;
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    $data = curl_exec($curl);
    return $data;
}