<?php
// this file will call required URL of scheduler module to trigger scheduler from outside
// for testing purpose let's first call http://eventhubsacramento.com/writeinlog.php

//uncomment below for going live
callScheduler();
writeInLog();

function writeInLog()
{
	//$url = "http://eventhubsacramento.com/writeinlog.php";
	$url = "http://eventhubsacramento.com/index.php?module=scheduler&type=user&func=test";
	$content = getUrlContent($url);
	echo $content;
}

function callScheduler()
{
	$url = "http://eventhubsacramento.com/index.php?module=scheduler&type=user&func=main";
	$content = getUrlContent($url);
	echo $content;
}

function getUrlContent($url, $loop = 0, $delay = 0)
{
        $file_contents = "";
	for($loopCount = 0; $loopCount <= $loop; $loopCount++)
{
    $ch = curl_init($url);
    $timeout = 10;
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        
        $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode['http_code'] < "300") 
    {
	if ($file_contents != '' || $loop == 0)
	{
		break;
	}
    }
    else 
    {
        $file_contents = "";
    }
	sleep($delay);
}   
    return $file_contents;


}
?>
