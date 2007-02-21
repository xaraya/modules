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
function ajax_libapi_includes($args)
{
    extract($args);

    if( !isset($name) ){ $name = "prototype"; }

    $libs_needed = array('prototype');

    if( $name == 'scriptaculous' )
    {
        $libs_needed[] = 'builder';
        $libs_needed[] = 'controls';
        $libs_needed[] = 'dragdrop';
        $libs_needed[] = 'effects';
        $libs_needed[] = 'scriptaculous';
        $libs_needed[] = 'slider';
    }
    elseif( $name != 'prototype' )
    {
        if( $name == 'validation' )
        {
            require_once "modules/themes/xarclass/xarcss.class.php";
            $css_args = array(
                'scope'    => 'module'
                , 'module' => 'ajax'
                , 'file'   => 'validation'
            );
            $obj = new xarCSS($css_args);
            $obj->run_output();
        }
        elseif( $name == 'lightbox' )
        {
            require_once "modules/themes/xarclass/xarcss.class.php";
            $css_args = array(
                'scope'    => 'module'
                , 'module' => 'ajax'
                , 'file'   => 'lightbox'
            );
            $obj = new xarCSS($css_args);
            $obj->run_output();

            $libs_needed[] = 'effects';
        }
        $libs_needed[] = $name;
    }

    foreach( $libs_needed as $lib )
    {
        xarModAPIFunc('base', 'javascript', 'modulefile',
            array('module' => 'ajax', 'filename' => "$lib.js", 'position' => 'head'));
    }

    return;
}
?>