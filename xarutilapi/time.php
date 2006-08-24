<?php
function maps_utilapi_time($args)
{
	extract($args);
	if (!isset($timestamp)) return time();
    return time();
}
?>

