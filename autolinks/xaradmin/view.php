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
    $links = xarModAPIFunc(
        'autolinks', 'user', 'getall',
        array(
            'startnum' => $startnumitem,
            'numitems' => xarModGetVar('autolinks', 'itemsperpage')
        )
    );

    // Check individual permissions for Edit/Delete/Enable
    $authid = xarSecGenAuthKey();

    if (is_array($links)) {
        $counttypes = xarModAPIfunc('autolinks', 'user', 'counttypes');
        foreach($links as $lid => $link) {
            if (xarSecurityCheck('EditAutolinks', 0)) {
                $links[$lid]['editurl'] = xarModURL(
                    'autolinks', 'admin', 'modify',
                    array('lid' => $lid, 'startnumitem' => $startnumitem)
                );
                // A move is appropriate only if there are other link types to
                // move the link to.
                if ($counttypes > 1) {
                    $links[$lid]['moveurl'] = xarModURL(
                        'autolinks', 'admin', 'move',
                        array('lid' => $lid)
                    );
                } else {
                    $links[$lid]['moveurl'] = '';
                }
                $links[$lid]['edittypeurl'] = xarModURL(
                    'autolinks', 'admin', 'modifytype',
                    array('tid' => $link['tid'])
                );
                $links[$lid]['enableurl'] = xarModURL(
                    'autolinks', 'admin', 'enable',
                    array(
                        'lid' => $lid,
                        'startnumitem' => $startnumitem,
                        'authid' => $authid
                    )
                );
            } else {
                $links[$lid]['editurl'] = '';
                $links[$lid]['moveurl'] = '';
                $links[$lid]['edittypeurl'] = '';
                $links[$lid]['enableurl'] = '';
            }

            if (xarSecurityCheck('DeleteAutolinks', 0)) {
                $links[$lid]['deleteurl'] = xarModURL(
                    'autolinks', 'admin', 'delete',
                    array('lid' => $lid)
                );
            } else {
                $links[$lid]['deleteurl'] = '';
            }

            // If we are displaying samples, then autolink match the samples.
            if (xarModGetVar('autolinks', 'showsamples')) {
                // Set a session var to indicate errors should be shown.
                xarVarSetCached('autolinks', 'showerrors', '1');
                if (!empty($links[$lid]['sample'])) {
                    // We need to perform a single autolink on the sample.
                    $links[$lid]['sampleresult'] = xarModAPIfunc(
                        'autolinks', 'user', '_transform',
                        array('text' => $links[$lid]['sample'], 'lid' => $lid)
                    );
                } else {
                    $links[$lid]['sampleresult'] = '';
                }
            }
        }
        // Add the array of items to the template variables
        $data['items'] = $links;
    }

    // Return the template variables defined in this function
    return $data;
}

?>
