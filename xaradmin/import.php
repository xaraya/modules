<?php
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
