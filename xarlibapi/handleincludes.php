<?php
/**
 * Ajax Library - A Prototype library collection.
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license   New BSD License
 * @link http://www.xaraya.com
 *
 * @subpackage Ajax Library Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */

function ajax_libapi_handleincludes($args)
{
    extract($args);
    if( !isset($name) ){ $name = "prototype"; }
    $out = "xarModAPIFunc("
    . "'ajax', 'lib', 'includes', "
    . "array('name'=>'" . $name . "')); ";
    return $out;
}
?>