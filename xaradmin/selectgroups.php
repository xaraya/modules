<?php

/**
 * modify configuration
 */
function newsgroups_admin_selectgroups()
{
    // Security Check
    if(!xarSecurityCheck('AdminNewsGroups')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch(strtolower($phase)) {

        case 'modify':
        default:
            // update the wildcard match if necessary
            if (!xarVarFetch('wildmat','isset',$wildmat,null,XARVAR_NOT_REQUIRED)) return;
            if (isset($wildmat)) {
                if (!empty($wildmat) && count($wildmat) > 0) {
                    $cleanmat = array();
                    foreach ($wildmat as $pattern) {
                        if (empty($pattern)) continue;
                        $pattern    = xarVarPrepForDisplay($pattern);
                        $cleanmat[] = trim($pattern);
                    }
                    $joinmat = join(',',$cleanmat);
                    xarModSetVar('newsgroups', 'wildmat', $joinmat);
                } else {
                    xarModSetVar('newsgroups', 'wildmat', '');
                }
            }
            // get the current list of newsgroups
            $data['items'] = xarModAPIFunc('newsgroups','user','getgroups',
                                           array('nocache' => true));
            $grouplist = xarModGetVar('newsgroups','grouplist');
            if (!empty($grouplist)) {
                $selected = unserialize($grouplist);
                // get list of selected newsgroups
                $data['selected'] = array_keys($selected);
                // update description of selected newsgroups
                foreach ($selected as $group => $info) {
                    if (isset($data['items'][$group]) && isset($info['desc'])) {
                        $data['items'][$group]['desc'] = $info['desc'];
                    }
                }
            } else {
                $data['selected'] = '';
            }
            $wildmat = xarModGetVar('newsgroups', 'wildmat');
            if (!empty($wildmat)) {
                $data['wildmat'] = explode(',',$wildmat);
            } else {
                $data['wildmat'] = array();
            }
            $data['wildmat'][] = '';
            $data['authid'] = xarSecGenAuthKey();

            break;

        case 'update':

            if (!xarVarFetch('selected','isset',$selected,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('desc','isset',$desc,'',XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            $grouplist = array();
            if (!empty($selected) && is_array($selected)) {
                $selectlist = array_keys($selected);
                if (!empty($selectlist)) {
                    $items = xarModAPIFunc('newsgroups','user','getgroups',
                                           array('nocache' => true));
                    foreach ($selectlist as $group) {
                        if (isset($items[$group])) {
                            $grouplist[$group] = $items[$group];
                            if (isset($desc[$group])) {
                                $grouplist[$group]['desc'] = $desc[$group];
                            }
                        }
                    }
                }
            }
            if (!empty($grouplist)) {
                xarModSetVar('newsgroups','grouplist',serialize($grouplist));
            } else {
                xarModSetVar('newsgroups','grouplist','');
            }

            // Return
            xarResponseRedirect(xarModURL('newsgroups', 'admin', 'selectgroups'));
            return true;

            break;
    }

    return $data;
}
?>
