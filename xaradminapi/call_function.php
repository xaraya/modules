<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function commerce_adminapi_call_function($args)
{
    extract($args);
    if(!isset($function)
        || !isset($parameter)) {
        $msg = xarML('Wrong arguments to commerce_userapi_get_products_price');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                    'BAD_PARAM',
                     new SystemException($msg));
        return false;
    }
    if(!isset($object)) $object = '';
    if ($object == '') {
        $func = explode('_',$function);
        $func[2] = substr($function,strlen($func[0] . '_' . $func[1])+1);
        return xarModAPIFunc($func[0],substr($func[1],0,strlen($func[1])-3),$func[2],array('value' => $parameter));
    } else {
        return call_user_func(array($object, $function), $parameter);
    }
  }
}
?>