<?php
/**
* Get URL-safe path
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Get URL-safe path
*
* @author Curtis Farnham <curtis@farnham.com>
* @access  public
* @param  $args ['path'] path string to clean up
* @return  array
* @returns item array
*/
function files_userapi_urlpath($args)
{
    extract($args);

    // validate path
    if (empty($path) || !is_string($path)) {
        return;
    }

    // divide path into its components
    $parts = explode('/', $path);

    // encode the individual parts
    $urlparts = array();
    foreach ($parts as $item) {
        $urlparts[] = rawurlencode($item);
    }

    // zip path back together
    $urlpath = join('/', $urlparts);

    return $urlpath;
}

?>
