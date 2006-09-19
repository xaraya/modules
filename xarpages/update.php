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
function xproject_pages_update($args)
{
    extract($args);

    if (!xarVarFetch('pageid', 'id', $pageid)) return;
    if (!xarVarFetch('parentid', 'id', $parentid, $parentid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('page_name', 'str:1:', $page_name, $page_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'id', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sequence', 'int::', $sequence, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str::', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('relativeurl', 'str::', $relativeurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    if(!xarModAPIFunc('xproject',
                    'pages',
                    'update',
                    array('pageid'            => $pageid,
                        'parentid'             => $parentid,
                        'page_name'         => $page_name,
                        'status'            => $status,
                        'description'       => $description,
                        'relativeurl'          => $relativeurl))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Page Updated'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'display', array('projectid' => $projectid, 'mode' => "pages")));

    return true;
}

?>