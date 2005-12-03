<?php
/**
 * File: $Id:
 *
 * Standard function to modify configuration parameters
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function bible_admin_modifyconfig()
{
    // security check
    if (!xarSecurityCheck('AdminBible')) return;

    // get vars
    $authid                   = xarSecGenAuthKey();
    $admin_textsperpage       = xarModGetVar('bible', 'admin_textsperpage');
    $user_searchversesperpage = xarModGetVar('bible', 'user_searchversesperpage');
    $user_lookupversesperpage = xarModGetVar('bible', 'user_lookupversesperpage');
    $user_wordsperpage        = xarModGetVar('bible', 'user_wordsperpage');
    $textdir                  = xarModGetVar('bible', 'textdir');
    $supportshorturls         = xarModGetVar('bible', 'SupportShortURLs');
    $usemodulealias           = xarModGetVar('bible', 'useModuleAlias');
    $aliasname                = xarModGetVar('bible', 'aliasname');
    $altdb                    = xarModGetVar('bible', 'altdb');
    $altdbtype                = xarModGetVar('bible', 'altdbtype');
    $altdbhost                = xarModGetVar('bible', 'altdbhost');
    $altdbname                = xarModGetVar('bible', 'altdbname');
    $altdbuname               = xarModGetVar('bible', 'altdbuname');
    $altdbpass                = xarModGetVar('bible', 'altdbpass');

    // get hooks
    $hookoutput = xarModCallHooks(
        'module', 'modifyconfig', 'bible', array('module' => 'bible')
    );

    // initialize template vars
    $data = xarModAPIFunc('bible', 'admin', 'menu');

    // set module vars
    $data['admin_textsperpage']       = $admin_textsperpage;
    $data['user_searchversesperpage'] = $user_searchversesperpage;
    $data['user_lookupversesperpage'] = $user_lookupversesperpage;
    $data['user_wordsperpage']        = $user_wordsperpage;
    $data['textdir']                  = $textdir;
    $data['supportshorturls']         = $supportshorturls;
    $data['usemodulealias']           = $usemodulealias;
    $data['aliasname']                = $aliasname;
    $data['altdb']                    = $altdb;
    $data['altdbtype']                = $altdbtype;
    $data['altdbhost']                = $altdbhost;
    $data['altdbname']                = $altdbname;
    $data['altdbuname']               = $altdbuname;
    $data['altdbpass']                = $altdbpass;

    // set other vars
    $data['authid'] = $authid;
    $data['hookoutput'] = &$hookmodules;

    return $data;
}

?>
