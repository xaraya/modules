<?php
/**
* Display details for an item
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
* Display details for an item
*
* @param  string $args['path'] Item to display, with relative path
*/
function files_user_display($args)
{
    // security check
    if (!xarSecurityCheck('ReadFiles', 1)) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('path', 'str:0:', $path, '/', XARVAR_NOT_REQUIRED)) return;

    // clean up the path and validate it
    $path = xarModAPIFunc('files', 'user', 'cleanpath', array('path' => $path));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get info on this file
    $item = xarModAPIFunc('files', 'user', 'get', array('path' => $path));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // prepare for setting options
    $archive_dir = xarModGetVar('files', 'archive_dir');
    $realpath = realpath($archive_dir)."/$path";
    $text_mimes = xarModAPIFunc('files', 'user', 'getmimetext');

    // generate options menu
    $options = array();
    if (xarSecurityCheck('ViewFiles', 0) && !is_dir($realpath) && is_readable($realpath)) {
        $options['view'] = true;
    }
    if (xarSecurityCheck('EditFiles', 0) && $path != '/' && in_array($item['mime'], $text_mimes) && is_writable($realpath)) {
        $options['edit'] = true;
    }
    if (xarSecurityCheck('DeleteFiles', 0) && $path != '/' && is_writable($realpath)) {
        $options['delete'] = true;
    }

    // set page title
    xarTplSetPageTitle(xarVarPrepForDisplay($path));

    // initialize template data array
    $data = xarModAPIFunc('files', 'user', 'menu');

    // generate template data array
    $data['path'] = $path;
    $data['item'] = $item;
    $data['options'] = $options;
    $data['pathparts'] = xarModAPIFunc('files', 'user', 'getfilepager', array('path' => $path));

    return $data;
}

?>
