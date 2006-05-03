<?php
/**
* Main user function
*
* @package modules
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Main user function
*
* Show directory listing and options
*
* @param  string $args['path'] Folder to be listed
*/
function files_user_main($args)
{
    // security check
    if (!xarSecurityCheck('ViewFiles')) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('path', 'str:0:', $path, '', XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (!isset($path)) $path = '';

    // clean and validate path
    $path = xarModAPIFunc('files', 'user', 'cleanpath', array('path' => $path));
    if (empty($path) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get other vars
    $archive_dir = xarModGetVar('files', 'archive_dir');

    if (empty($archive_dir)) {
        $msg = xarML('Empty archive directory for #(1) function #(2)() in module #(3)',
            'user', 'main', 'Files');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // if path exists but is a regular file, redirect to display function
    if (!is_dir("$archive_dir/$path")) {
        xarResponseRedirect(xarModURL('files', 'user', 'display', array('path' => $path)));
        return true;
    }

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

    // set page title
    xarTplSetPageTitle(xarVarPrepForDisplay($path));

    // initialize template data array
    $data = xarModAPIFunc('files', 'user', 'menu');

    // generate template vars
    $data['path'] = $path;
    $data['urlpath'] = xarModAPIFunc('files', 'user', 'urlpath', array('path' => $path));
    $data['files'] = xarModAPIFunc('files', 'user', 'getall', array('path' => $path));
    $data['pathparts'] = xarModAPIFunc('files', 'user', 'getfilepager', array('path' => $path));
    $data['options'] = $options;

    return $data;
}

?>
