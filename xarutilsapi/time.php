<?php
function maps_utilsapi_time($args)
{
	extract($args);
	if (!isset($timestamp)) return time();
    return time();
}
?>

