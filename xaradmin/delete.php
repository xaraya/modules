<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * delete item
 */
function articles_admin_delete()
{
    // Get parameters
    if (!xarVarFetch('aid', 'id', $aid)) return;
    if (!xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'str:1', $return_url, NULL, XARVAR_NOT_REQUIRED)) {return;}

    // Get article information
    $article = xarMod::apiFunc('articles',
                             'user',
                             'get',
                             array('aid' => $aid,
                                   'withcids' => true));
    if (!isset($article) || $article == false) {
        $msg = xarML('Unable to find #(1) item #(2)',
                     'Article', xarVarPrepForDisplay($aid));
        throw new ForbiddenOperationException(null, $msg);
    }

    $ptid = $article['pubtypeid'];

    // Security check
    $input = array();
    $input['article'] = $article;
    $input['mask'] = 'DeleteArticles';
    if (!xarMod::apiFunc('articles','user','checksecurity',$input)) {
        $pubtypes = xarMod::apiFunc('articles','user','getpubtypes');
        $msg = xarML('You have no permission to delete #(1) item #(2)',
                     $pubtypes[$ptid]['descr'], xarVarPrepForDisplay($aid));
        throw new ForbiddenOperationException(null, $msg);
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

        $data['return_url'] = $return_url;

        $pubtypes = xarMod::apiFunc('articles','user','getpubtypes');
        $template = $pubtypes[$ptid]['name'];

        // Return the template variables defined in this function
        return xarTplModule('articles', 'admin', 'delete', $data, $template);
    }

    // Confirmation present
    if (!xarSecConfirmAuthKey()) {
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
    }        

    // Pass to API
    if (!xarMod::apiFunc('articles',
                     'admin',
                     'delete',
                     array('aid' => $aid,
                           'ptid' => $ptid))) {
        return;
    }

    // Success
    xarSession::setVar('statusmsg', xarML('Article Deleted'));

    // Return return_url
    if (!empty($return_url)) {
        xarResponse::Redirect($return_url);
        return true;
    }

    // Return to the original admin view
    $lastview = xarSession::getVar('Articles.LastView');
    if (isset($lastview)) {
        $lastviewarray = unserialize($lastview);
        if (!empty($lastviewarray['ptid']) && $lastviewarray['ptid'] == $ptid) {
            extract($lastviewarray);
            xarResponse::Redirect(xarModURL('articles', 'admin', 'view',
                                          array('ptid' => $ptid,
                                                'catid' => $catid,
                                                'status' => $status,
                                                'startnum' => $startnum)));
            return true;
        }
    }

    xarResponse::Redirect(xarModURL('articles', 'admin', 'view',
                                  array('ptid' => $ptid)));

    return true;
}

?>