<?php
/**
 * Generate Ephemerids listing for display
 */
function ephemerids_admin_view()
{
    // Get parameters from whatever input we need
    $startnum = xarVarCleanFromInput('startnum');
    $data['items'] = array();

    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;

    // Specify some labels for display
    $data['daylabel'] = xarVarPrepForDisplay(xarML('Day'));
    $data['monthlabel'] = xarVarPrepForDisplay(xarML('Month'));
    $data['yearlabel'] = xarVarPrepForDisplay(xarML('Year'));
    $data['eventlabel'] = xarVarPrepForDisplay(xarML('Event'));
    $data['languagelabel'] = xarVarPrepForDisplay(xarML('Language'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('ephemerids', 'user', 'countitems'),
                                    xarModURL('ephemerids', 'admin', 'view', array('startnum' => '%%')),
                                    xarModGetVar('ephemerids', 'itemsperpage'));

    // The admin API function is called. 
    $ephemlist = xarModAPIFunc('ephemerids',
                               'admin',
                               'display');
    // Check for exceptions
    if (!isset($ephemlist) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($ephemlist); $i++) {
        $ephem1 = $ephemlist[$i];
        if (xarSecurityCheck('EditEphemerids',0)) {
            $ephemlist[$i]['editurl'] = xarModURL('ephemerids',
                                             'admin',
                                             'modify',
                                             array('eid' => $ephem1['eid']));
        } else {
            $ephemlist[$i]['editurl'] = '';
        }
        $ephemlist[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteEphemerids',0)) {
            $ephemlist[$i]['deleteurl'] = xarModURL('ephemerids',
                                               'admin',
                                               'delete',
                                               array('eid' => $ephem1['eid']));
        } else {
            $ephemlist[$i]['deleteurl'] = '';
        }
        $ephemlist[$i]['deletetitle'] = xarML('Delete');
    }

    // Add the array of items to the template variables
    $data['ephemlist'] = $ephemlist;

    // Return the template variables defined in this function
    return $data;
}

?>