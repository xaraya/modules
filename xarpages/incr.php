<?php

function xproject_pages_incr($args)
{
    if(!xarVarFetch('pageid','int', $pageid, $pageid, XARVAR_NOT_REQUIRED)) {return;}

    extract($args);

    $pageinfo = xarModAPIFunc('xproject', 'pages', 'get', array('pageid' => $pageid));

    if(!xarModAPIFunc('xproject',
                    'pages',
                    'incr',
                    array('pageid' => $pageid))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Page Incremented'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'display', array('projectid' => $pageinfo['projectid'], 'mode' => "pages")));

    return true;
}

?>