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
function headlines_admin_importitem()
{
    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    if (!xarVarFetch('title','str:1:', $title)) return;
    if (!xarVarFetch('description','str:1:', $description)) return;
    if (!xarVarFetch('hid','int', $hid)) return;
    $importpubtype = xarModGetVar('headlines','importpubtype');
    if (empty($importpubtype)) {
        xarResponseRedirect(xarModURL('headlines', 'user', 'view', array('hid' => $hid)));
        return true;
    }
    $article['title'] = $title;
    $article['summary'] = $description;
    $article['aid'] = 0;
    $article['ptid'] = $importpubtype;
    $article['status'] = 2;
    xarModAPIFunc('articles', 'admin', 'create', $article);
    xarResponseRedirect(xarModURL('headlines', 'user', 'view', array('hid' => $hid)));
}
?>
