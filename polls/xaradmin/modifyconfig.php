<?php

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function polls_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminPolls')) {
        return;
    }

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // everything else happens in Template for now
    // prepare labels and values for display by the template

    $data['barscale'] = xarModGetVar('polls', 'barscale');

    $data['itemsperpage'] = xarModGetVar('polls', 'itemsperpage');

    $data['defaultopts'] = xarModGetVar('polls', 'defaultopts');

    $data['comments'] = xarModGetVar('polls', 'comments');

    $data['previewresults'] = xarModGetVar('polls', 'previewresults');

    $data['imggraphs'] = array();
    $data['imggraphs']['0'] = xarML('Never');
    $data['imggraphs']['1'] = xarML('Blocks only');
    $data['imggraphs']['2'] = xarML('Module space only');
    $data['imggraphs']['3'] = xarML('Always');
    $data['imggraph'] =  xarModGetVar('polls', 'imggraph');

    $data['voteintervals'] = array();
    $data['voteintervals']['-1'] = xarML('Once');
    $data['voteintervals']['86400'] = xarML('Once per day');
    $data['voteintervals']['604800'] = xarML('Once per week');
    $data['voteintervals']['2592000'] = xarML('Once per month');
    $data['voteinterval'] =  xarModGetVar('polls', 'voteinterval');

    return $data;
}

?>