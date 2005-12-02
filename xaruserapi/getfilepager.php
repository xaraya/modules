<?php
/**
* Get details on a file or folder
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
* Get details on a file or folder
*
* @author Curtis Farnham <curtis@farnham.com>
* @access  public
* @param   string $args['path'] relative path of folder to list
* @return  array
* @returns item array, or false on failure
* @throws  NO_PERMISSION
*/
function files_userapi_getfilepager($args)
{
    // security check
    if (!xarSecurityCheck('ViewFiles')) return;

    extract($args);

    // set defaults
    if (empty($path)) $path = '/';

    // split path into its parts and assemble list of intermediate paths
    $parts = explode('/', preg_replace("/(^\/|\/\$)/", '', $path));
    $pathparts = array();
    for ($i=0; $i<count($parts); $i++) {
        $pathparts[$parts[$i]] = '/'.join('/', array_slice($parts, 0, $i+1));
    }

    return $pathparts;

}

?>
