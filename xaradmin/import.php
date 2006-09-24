<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
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

    $importpubtype = xarModGetVar('headlines','importpubtype');
    if (empty($importpubtype)) {
        xarResponseRedirect(xarModURL('headlines', 'admin', 'modifyconfig'));
        return true;
    }

    $imported = xarModAPIFunc('headlines','admin','import',
                              array('hid' => $hid,
                                    'iid' => $iid,
                                    'importpubtype' => $importpubtype));
    if (!isset($imported)) return;

    if (empty($imported)) {
        xarResponseRedirect(xarModURL('headlines', 'user', 'view', array('hid' => $hid)));
    } else {
        xarResponseRedirect(xarModURL('articles', 'admin', 'view', array('ptid' => $importpubtype)));
    }
    return true;
}
?>
