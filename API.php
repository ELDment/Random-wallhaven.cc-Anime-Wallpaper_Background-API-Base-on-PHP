<?php
@header('Content-type: image/png');
ini_set('display_errors', 0);
$web = 'https://wallhaven.cc/search?categories=010&purity=110&resolutions=1920x1080&ratios=16x9&sorting=random&order=desc';
function get_html()
{
    global $web;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $web);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    if ($data == '')
    {
        http_response_code(502);
        exit(502);
    }
    return $data;
}
function get_href($data)
{
    preg_match_all('/href=([\'"]?)([^\s]+)\\1/', $data, $arr);
    return(array($arr)[0][2]);
}
function get_tags($data)
{
    $arr = array();
    for ($i = 0; $i < count($data); $i++)
    {
        $url = trim($data[$i]);
        if (stripos($url, 'https://wallhaven.cc/wallpaper/tags') !== false)
        {
            array_push($arr, $url);
        }
    }
    return $arr;
}
function get_one()
{
    $arr = get_tags(get_href(get_html()));
    return $arr[rand(0, count($arr) - 1)];
}
function wallpaper($url)
{
    if ($url == '')
    {
        http_response_code(502);
        exit(502);
    }
    $id = trim(str_ireplace('https://wallhaven.cc/wallpaper/tags/', '', $url));
    $jpg = "https://w.wallhaven.cc/full/".substr($id, 0, 2)."/wallhaven-$id.jpg";
    return $jpg;
}
imagejpeg(imagecreatefromjpeg(wallpaper(get_one())));
?>