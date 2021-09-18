<?php

$resourcesDir = dirname(__FILE__) . '/../../../resources';

$wurfl['main-file'] = $resourcesDir . "/wurfl-regression.xml";
$wurfl['patches'] = [$resourcesDir . "/web_browsers_patch.xml", $resourcesDir."/spv_patch.xml"];

$persistence['provider'] = "memcache";
$persistence['params'] = [
    "dir" => "cache",
];

$cache['provider'] = "null";


$configuration["wurfl"] = $wurfl;
$configuration["allow-reload"] = true;
$configuration["persistence"] = $persistence;
$configuration["cache"] = $cache;
