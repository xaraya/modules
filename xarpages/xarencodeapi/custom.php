<?php

function xarpages_encodeapi_custom($args) {
    extract($args);

    $path = array();
    $get = $args;

    if (!empty($catid)) {
        $path[] = $catid;
        unset($get['catid']);
    }

    return array(
        'path' => $path,
        'get' => $get
    );
}

?>