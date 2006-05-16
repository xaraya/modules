<?php
/**
* Delete an item
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
* Delete an item
*
* @param integer $args['confirm'] Confirm deletion
* @param string $args['path'] Item to delete, with relative path
*/
function files_user_delete($args)
{
    // security check
    if (!xarSecurityCheck('DeleteFiles', 1)) return;

    extract($args);

    // get HTTP input vars
    if (!xarVarFetch('path', 'str:0:', $path)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // clean up the path and prepare to validate it
    $path = xarModAPIFunc('files', 'user', 'cleanpath', array('path' => $path));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // retrieve info on this file
    $item = xarModAPIFunc('files', 'user', 'get', array('path' => $path));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // show GUI if we haven't confirmed
    if (empty($confirm)) {

        $archive_dir = xarModGetVar('files', 'archive_dir');

        if (is_dir("$archive_dir/$path")) {

            // generate options
            $options = array();
            if (is_writable("$archive_dir/$path")) {
                if (xarSecurityCheck('AddFiles', 0)) {
                    $options['add'] = true;
                }
                if (xarSecurityCheck('DeleteFiles', 0)) {
                    $options['delete'] = true;
                }
            }
        } else {

            $realpath = $item['realpath'];
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

        }

        // initialize template vars
        $data = xarModAPIFunc('files', 'user', 'menu');

        // add template vars
        $data['authid'] = xarSecGenAuthKey();
        $data['path'] = $path;
        $data['item'] = $item;
        $data['urlpath'] = xarModAPIFunc('files', 'user', 'urlpath',
            array('path' => $path));
        $data['pathparts'] = xarModAPIFunc('files', 'user', 'getfilepager', array('path' => $path));
        $data['options'] = $options;

        return $data;
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // call API function to delete this file
    if (!xarModAPIFunc('files', 'user', 'delete', array('path' => $path))) {
        return;
    }

    // set status and return to the folder we were in
    xarSessionSetVar('statusmsg', xarML('Item successfully deleted!'));
    xarResponseRedirect(xarModURL('files', 'user', 'main', array('path' => dirname($path))));

    // success
    return true;
}

?>
