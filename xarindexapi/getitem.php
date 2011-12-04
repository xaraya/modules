<?php
function keywords_indexapi_getitem(Array $args=array())
{
    if (empty($args)) {
        $msg = 'Missing #(1) for #(2) module #(3) function #(4)()';
        $vars = array('arguments', 'keywords', 'indexapi', 'getitem');
        throw new EmptyParameterException($vars, $msg);
    }

    $items = xarMod::apiFunc('keywords', 'index', 'getitems', $args);

    if (empty($items)) {
        return false;
    } elseif (count($items) > 1) {
        $msg = 'Missing or invalid #(1) for #(2) module #(3) function #(4)()';
        $vars = array('arguments', 'keywords', 'indexapi', 'getitem');
        throw new EmptyParameterException($vars, $msg);
    } else {
        return reset($items);
    }
}
?>