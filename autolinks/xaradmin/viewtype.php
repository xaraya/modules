<?php

/**
 * $Id$
 * view autolink types
 */
function autolinks_admin_viewtype()
{
    // Get parameters from whatever input we need
    xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_NOT_REQUIRED);

    $data['items'] = array();
    $data['authid'] = xarSecGenAuthKey();

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnumitem,
                                    xarModAPIFunc('autolinks', 'user', 'counttypes'),
                                    xarModURL('autolinks', 'admin', 'viewtype', array('startnumitem' => '%%')),
                                    xarModGetVar('autolinks', 'itemsperpage'));

    // Security Check
    if(!xarSecurityCheck('EditAutolinks')) return;

    // The user API function is called
    $links = xarModAPIFunc('autolinks',
                          'user',
                          'getalltypes',
                          array('startnum' => $startnumitem,
                                'numitems' => xarModGetVar('autolinks',
                                                          'itemsperpage')));

    if (empty($links)) {
        $msg = xarML('No Autolink types in database.', 'autolinks');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // TODO: sort the link types by something. Name? User-defined?

    // Check individual permissions for Edit/Delete/Enable
    $authid = xarSecGenAuthKey();

    foreach ($links as $i => $link) {
        if (xarSecurityCheck('EditAutolinks',0)) {
            $links[$i]['editurl'] = xarModURL('autolinks',
                                             'admin',
                                             'modifytype',
                                             array('tid' => $link['tid'], 'startnumitem' => $startnumitem));
        } else {
            $links[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteAutolinks',0)) {
            $links[$i]['deleteurl'] = xarModURL('autolinks',
                                               'admin',
                                               'deletetype',
                                               array('tid' => $link['tid']));
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
