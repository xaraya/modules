<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */

function gallery_userapi_get_watermarks($args)
{
    $all_watermarks =@ unserialize(xarModGetVar('gallery', 'watermarks'));

    if( !is_array($all_watermarks) )
        $all_watermarks = array();

    return $all_watermarks;
}
?>