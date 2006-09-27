<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_pages_create($args)
{
    extract($args);

    if (!xarVarFetch('page_name', 'str:1:', $page_name, $page_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('parentid', 'id', $parentid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'id', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sequence', 'int::', $sequence, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str::', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('relativeurl', 'str::', $relativeurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    $pageid = xarModAPIFunc('xproject',
                        'pages',
                        'create',
                        array('page_name'         => $page_name,
                            'parentid'          => $parentid,
                            'projectid'         => $projectid,
                            'status'            => $status,
                            'sequence'            => $sequence,
                            'description'       => $description,
                            'relativeurl'          => $relativeurl));


    if (!isset($pageid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('PROJECTCREATED'));

    if($parentid > 0) {
        xarResponseRedirect(xarModURL('xproject', 'pages', 'display', array('pageid' => $parentid, 'mode' => "pages")));
    } else {
        xarResponseRedirect(xarModURL('xproject', 'admin', 'display', array('projectid' => $projectid, 'mode' => "pages")));
    }
    
    return true;
}

?>
