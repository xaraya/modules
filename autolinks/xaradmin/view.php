<?php

/**
 * $Id$
 * view items
 */
function autolinks_admin_view()
{
    // Get parameters from whatever input we need
    xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_NOT_REQUIRED);

    $data['items'] = array();
    $data['authid'] = xarSecGenAuthKey();

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnumitem,
                                    xarModAPIFunc('autolinks', 'user', 'countitems'),
                                    xarModURL('autolinks', 'admin', 'view', array('startnumitem' => '%%')),
                                    xarModGetVar('autolinks', 'itemsperpage'));

    // Security Check
    if(!xarSecurityCheck('EditAutolinks')) return;

    // The user API function is called
    $links = xarModAPIFunc('autolinks',
                          'user',
                          'getall',
                          array('startnum' => $startnumitem,
                                'numitems' => xarModGetVar('autolinks',
                                                          'itemsperpage')));

    if (empty($links)) {
        $msg = xarML('No Autolinks in database.', 'autolinks');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check individual permissions for Edit/Delete/Enable
    $authid = xarSecGenAuthKey();

    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        if (xarSecurityCheck('EditAutolinks',0)) {
            $links[$i]['editurl'] = xarModURL('autolinks',
                                             'admin',
                                             'modify',
                                             array('lid' => $link['lid'], 'startnumitem' => $startnumitem));
            $links[$i]['enableurl'] = xarModURL(
                                            'autolinks',
                                            'admin',
                                            'enable',
                                            array(
                                                'lid' => $link['lid'],
                                                'startnumitem' => $startnumitem,
                                                'authid' => $authid
                                            )
                                        );
        } else {
            $links[$i]['editurl'] = '';
        }
        $links[$i]['enablelabel'] = xarML(empty($link['enabled']) ? 'Enable' : 'Disable', 'autolinks');
        if (xarSecurityCheck('DeleteAutolinks',0)) {
            $links[$i]['deleteurl'] = xarModURL('autolinks',
                                               'admin',
                                               'delete',
                                               array('lid' => $link['lid']));
        } else {
            $links[$i]['deleteurl'] = '';
        }
    }

    // Add the array of items to the template variables
    $data['items'] = $links;

    // Return the template variables defined in this function
    return $data;
}

?>
