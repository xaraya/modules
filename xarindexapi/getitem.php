<?php

function keywords_indexapi_getitem(array $args=[])
{
    if (empty($args)) {
        $msg = 'Missing #(1) for #(2) module #(3) function #(4)()';
        $vars = ['arguments', 'keywords', 'indexapi', 'getitem'];
        throw new EmptyParameterException($vars, $msg);
    }

    $items = xarMod::apiFunc('keywords', 'index', 'getitems', $args);

    if (empty($items)) {
        return false;
    } elseif (count($items) > 1) {
        $msg = 'Missing or invalid #(1) for #(2) module #(3) function #(4)()';
        $vars = ['arguments', 'keywords', 'indexapi', 'getitem'];
        throw new EmptyParameterException($vars, $msg);
    } else {
        return reset($items);
    }
}
