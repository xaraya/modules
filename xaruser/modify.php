<?php
/**
* Display GUI to edit a text file
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
* Display GUI to edit a text file
*
* @param  string $args['path'] Path of file to edit, with relative path
*/
function files_user_modify($args)
{
    // security check
    if (!xarSecurityCheck('EditFiles')) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('path', 'str:1', $path, $path)) return;

    // set defaults
    if (!isset($path)) $path = '';

    // clean and validate path
    $path = xarModAPIFunc('files', 'user', 'cleanpath',
        array('path' => $path, 'type' => 'file', 'mode' => 'write'));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get item details
    $item = xarModAPIFunc('files', 'user', 'get', array('path' => $path));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // make sure file is plaintext
    $text_mimes = xarModAPIFunc('files', 'user', 'getmimetext');
    if (!in_array($item['mime'], $text_mimes)) {
        $msg = xarML('#(1) is not plain text.  Unable to edit.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // get contents of file for editing
    $contents = file_get_contents($item['realpath']);

    // count newlines for runtime configurability of textarea
    preg_match_all("/(\r\n|\n\r|\n|\r)/", $contents, $matches);
    $newlines = 0;
    if (!empty($matches[0])) $newlines = count($matches[0]);

    $archive_dir = xarModGetVar('files', 'archive_dir');
    $realpath = $item['realpath'];

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

    // initialize template data array
    $data = xarModAPIFunc('files', 'admin', 'menu');

    // generate template vars
    $data['path'] = $path;
    $data['item'] = $item;
    $data['pathparts'] = xarModAPIFunc('files', 'user', 'getfilepager', array('path' => $path));
    $data['urlpath'] = xarModAPIFunc('files', 'user', 'urlpath', array('path' => $path));
    $data['authid'] = xarSecGenAuthKey();
    $data['contents'] = $contents;
    $data['newlines'] = $newlines;
    $data['pathparts'] = xarModAPIFunc('files', 'user', 'getfilepager', array('path' => $path));
    $data['options'] = $options;

    return $data;
}
?>
