<?php

/**
 * delete item
 */
function articles_admin_delete()
{
    // Get parameters
    if (!xarVarFetch('aid', 'id', $aid)) return;
    if (!xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED)) return;

    // Get article information
    $article = xarModAPIFunc('articles',
                             'user',
                             'get',
                             array('aid' => $aid,
                                   'withcids' => true));
    if (!isset($article) || $article == false) {
        $msg = xarML('Unable to find #(1) item #(2)',
                     'Article', xarVarPrepForDisplay($aid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                        new SystemException($msg));
        return;
    }

    $ptid = $article['pubtypeid'];

    // Security check
    $input = array();
    $input['article'] = $article;
    $input['mask'] = 'DeleteArticles';
    if (!xarModAPIFunc('articles','user','checksecurity',$input)) {
        $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
        $msg = xarML('You have no permission to delete #(1) item #(2)',
                     $pubtypes[$ptid]['descr'], xarVarPrepForDisplay($aid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    // Check for confirmation
    if (!$confirm) {
        $data = array();

        // Specify for which item you want confirmation
        $data['aid'] = $aid;

        // Use articles user GUI function (not API) for preview
        if (!xarModLoad('articles','user')) return;
        $data['preview'] = xarModFunc('articles', 'user', 'display',
                                      array('preview' => true, 'article' => $article));

        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Confirm deleting this article');
        $data['confirmlabel'] = xarML('Confirm');

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // Confirmation present
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (!xarModAPIFunc('articles',
                     'admin',
                     'delete',
                     array('aid' => $aid,
                           'ptid' => $ptid))) {
        return;
    }

    // Success
    xarSessionSetVar('statusmsg', xarML('Article Deleted'));

    // Return to the original admin view
    $lastview = xarSessionGetVar('Articles.LastView');
    if (isset($lastview)) {
        $lastviewarray = unserialize($lastview);
        if (!empty($lastviewarray['ptid']) && $lastviewarray['ptid'] == $ptid) {
            extract($lastviewarray);
            xarResponseRedirect(xarModURL('articles', 'admin', 'view',
                                          array('ptid' => $ptid,
                                                'catid' => $catid,
                                                'status' => $status,
                                                'startnum' => $startnum)));
            return true;
        }
    }

    xarResponseRedirect(xarModURL('articles', 'admin', 'view',
                                  array('ptid' => $ptid)));

    return true;
}

?>
