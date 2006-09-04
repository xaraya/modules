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

function gallery_userapi_get_watermark($args)
{
    extract($args);

    if( !isset($watermark_id) ) return false;

    $watermarks = xarModAPIFunc('gallery', 'user', 'get_watermarks');

    if( !isset($watermarks[$watermark_id]) ) return array();

    $watermark = $watermarks[$watermark_id];

    return $watermark;
}
?>