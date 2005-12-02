<?php
/**
* Rename a file or folder
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
* Rename a file or folder
*
* If 'name' not given, show the GUI.  Otherwise, create it.
*
* @param  string $args['path'] Path of folder to rename, with relative path
* @param  string $args['name'] New name
* @param  string $args['itemtype'] Type of item to rename.  'folder' or 'file'
*/
function files_user_rename($args)
{
    // security check
    if (!xarSecurityCheck('AddFiles', 1)) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('path', 'str:0:', $path)) return;
    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'str:1:', $itemtype, '', XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (empty($itemtype)) {
        $itemtype = 'folder';
    }

    // don't rename the root dir!
    if (empty($path) || $path == '/') {
        return;
    }

    // clean and validate path
    $path = xarModAPIFunc('files', 'user', 'cleanpath',
        array('path' => $path, 'type' => 'file', 'mode' => 'write'));
    if (empty($path) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // if no name given, show the GUI
    if (empty($name)) {

        // initialize the template array
        $data = xarModAPIFunc('files', 'admin', 'menu');

        // generate template vars
        $data['path'] = $path;
        $data['authid'] = xarSecGenAuthKey();
        $data['itemtype'] = $itemtype;
        $data['urlpath'] = xarModAPIFunc('files', 'user', 'urlpath',
            array('path' => $path));

        return $data;
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // let the API function do the creating
    if (!xarModAPIFunc('files', 'user', 'rename',
        array('path' => $path, 'name' => $name, 'itemtype' => $itemtype))) return;

    // set status message and redirect to the renamed item
    $newpath = dirname($path)."/$name";
    xarSessionSetVar('statusmsg', xarML('Item successfully renamed!'));
    xarResponseRedirect(xarModURL('files', 'user', 'display', array('path' => $newpath)));

    // success
    return true;
}

?>
