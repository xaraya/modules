<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
function headlines_admin_import()
{
    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;

    // Feed id
    if (!xarVarFetch('hid', 'id', $hid)) return;
    // Headline id (optional)
    if (!xarVarFetch('iid', 'str:1:', $iid, '', XARVAR_NOT_REQUIRED)) return;

    $importpubtype = xarModVars::get('headlines','importpubtype');
    if (empty($importpubtype)) {
        xarResponse::Redirect(xarModURL('headlines', 'admin', 'modifyconfig'));
        return true;
    }

    $imported = xarMod::apiFunc('headlines','admin','import',
                              array('hid' => $hid,
                                    'iid' => $iid,
                                    'importpubtype' => $importpubtype));
    if (!isset($imported)) return;

    if (empty($imported)) {
        xarResponse::Redirect(xarModURL('headlines', 'user', 'view', array('hid' => $hid)));
    } else {
        xarResponse::Redirect(xarModURL('articles', 'admin', 'view', array('ptid' => $importpubtype)));
    }
    return true;
}
?>
