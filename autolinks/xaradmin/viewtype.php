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
    $data['pager'] = xarTplGetPager(
        $startnumitem,
        xarModAPIFunc('autolinks', 'user', 'counttypes'),
        xarModURL('autolinks', 'admin', 'viewtype', array('startnumitem' => '%%')),
        xarModGetVar('autolinks', 'itemsperpage')
    );

    // Security Check
    if(!xarSecurityCheck('EditAutolinks')) {return;}

    // The user API function is called
    $links = xarModAPIFunc(
        'autolinks', 'user', 'getalltypes',
        array(
            'startnum' => $startnumitem,
            'numitems' => xarModGetVar('autolinks', 'itemsperpage')
        )
    );

    // Check individual permissions for Edit/Delete/Enable
    $authid = xarSecGenAuthKey();

    if (is_array($links)) {
        foreach ($links as $i => $link) {
            if (xarSecurityCheck('EditAutolinks', 0)) {
                $links[$i]['linksurl'] = xarModURL(
                    'autolinks', 'admin', 'view',
                    array('tid' => $link['tid'])
                );
            } else {
                $links[$i]['linksurl'] = '';
            }
            if (xarSecurityCheck('EditAutolinks', 0)) {
                $links[$i]['editurl'] = xarModURL(
                    'autolinks', 'admin', 'modifytype',
                    array('tid' => $link['tid'], 'startnumitem' => $startnumitem)
                );
            } else {
                $links[$i]['editurl'] = '';
            }
            if (xarSecurityCheck('DeleteAutolinks', 0)) {
                $links[$i]['deleteurl'] = xarModURL(
                    'autolinks', 'admin', 'deletetype',
                    array('tid' => $link['tid'])
                );
            } else {
                $links[$i]['deleteurl'] = '';
            }
        }

        // Add the array of items to the template variables
        $data['items'] = $links;
    }

    // Return the template variables defined in this function
    return $data;
}

?>
