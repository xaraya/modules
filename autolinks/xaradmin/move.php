<?php

/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('autolinks','admin','new') to create a new item
 * @param 'keyword' the keyword of the link to be created
 * @param 'title' the title of the link to be created
 * @param 'url' the url of the link to be created
 * @param 'comment' the comment of the link to be created
 */
function autolinks_admin_move()
{
    // Takes a lid and allows it to be moved from one type to another.
    // Pass 1: takes a lid and displays options.
    // Pass 2: takes a lid and tid and displays confirm.
    // Pass 3: does the move.
    
    
    // A link ID is mandatory. The rest depend on the stage.
    if (!xarVarFetch('lid', 'id', $lid, NULL, XARVAR_DONT_SET)) {return;}
//    if (!xarVarFetch('tid', 'id', $tid, NULL, XAR_NOT_REQUIRED)) {return;}
//    if (!xarVarFetch('confirm', 'int', $confirm, NULL, XAR_NOT_REQUIRED)) {return;}

    $data = array();

    if (!empty($lid) && empty($tid)) {
        // Get the link details.
        $link = xarModAPIfunc(
            'autolinks', 'user', 'get',
            array('lid' => $lid)
        );

        if (!$link) {return;}

        // Get the list of link types.
        $types = xarModAPIfunc('autolinks', 'user', 'getalltypes');

        if (!$types) {return;}

        // There must be at least two types for a move.
        if (count($types) < 2) {
            // TODO: raise user error.
            return;
        }

        // Remove the current link type.
        $data['currenttype'] = $types[$link['tid']];
        unset ($types[$link['tid']]);

        $data['link'] = $link;
        $data['types'] = $types;

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }


    //xarResponseRedirect(xarModURL('autolinks', 'admin', 'modify', array('lid' => $lid)));

    // Return
    return true;
}

?>
