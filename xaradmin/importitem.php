<?php
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
