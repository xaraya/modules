<?php
function headlines_admin_import()
{
    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;

    if (!xarVarFetch('hid', 'id', $hid)) return;

    $importpubtype = xarModGetVar('headlines','importpubtype');
    if (empty($importpubtype)) {
        xarResponseRedirect(xarModURL('articles', 'admin', 'view'));
        return true;
    }

    // The user API function is called
    $links = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));


    // Check and see if a feed has been supplied to us.
    if(isset($links['url'])) {
        $feedfile = $links['url'];
    } else {
        $feedfile = "";
    }

    if (xarModGetVar('headlines', 'magpie')){
        $imports = xarModAPIFunc('magpie',
                              'user',
                              'process',
                              array('feedfile' => $feedfile));
    } else {
        $imports = xarModAPIFunc('headlines',
                              'user',
                              'process',
                              array('feedfile' => $feedfile));
    }

    if (!empty($imports['warning'])){
        $msg = xarML('There is a problem with this feed : #(1)', $info['warning']);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    foreach ($imports['feedcontent'] as $import){

        $article['title'] = $import['title'];
        $article['summary'] = $import['description'];
        $article['summary'] .= '<br /><br />';
        $article['summary'] .= xarML('Source');
        $article['summary'] .= ': <a href="';
        $article['summary'] .= $imports['chanlink'];
        $article['summary'] .= '">';
        $article['summary'] .= $imports['chantitle'];
        $article['summary'] .= '</a>';
        $article['aid'] = 0;
        $article['ptid'] = $importpubtype;
        xarModAPIFunc('articles', 'admin', 'create', $article);
    }
    
    xarResponseRedirect(xarModURL('articles', 'admin', 'view'));
    return true;
}
?>
