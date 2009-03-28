<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * delete item
 */
function publications_user_delete()
{
    // Get parameters
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'str:1', $return_url, NULL, XARVAR_NOT_REQUIRED)) {return;}

    // Get article information
    $article = xarModAPIFunc('publications',
                             'user',
                             'get',
                             array('id' => $id,
                                   'withcids' => true));
    if (!isset($article) || $article == false) {
        $msg = xarML('Unable to find #(1) item #(2)',
                     'Publication', xarVarPrepForDisplay($id));
        throw new ForbiddenOperationException(null, $msg);
    }

    $ptid = $article['pubtype_id'];

    // Security check
    $input = array();
    $input['article'] = $article;
    $input['mask'] = 'ManagePublications';
    if (!xarModAPIFunc('publications','user','checksecurity',$input)) {
        $pubtypes = xarModAPIFunc('publications','user','getpubtypes');
        $msg = xarML('You have no permission to delete #(1) item #(2)',
                     $pubtypes[$ptid]['description'], xarVarPrepForDisplay($id));
        throw new ForbiddenOperationException(null, $msg);
    }

    // Check for confirmation
    if (!$confirm) {
        $data = array();

        // Specify for which item you want confirmation
        $data['id'] = $id;

        // Use publications user GUI function (not API) for preview
        if (!xarModLoad('publications','user')) return;
        $data['preview'] = xarModFunc('publications', 'user', 'display',
                                      array('preview' => true, 'article' => $article));

        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Confirm deleting this article');
        $data['confirmlabel'] = xarML('Confirm');

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        $data['return_url'] = $return_url;

        $pubtypes = xarModAPIFunc('publications','user','getpubtypes');
        $template = $pubtypes[$ptid]['name'];

        // Return the template variables defined in this function
        return xarTplModule('publications', 'admin', 'delete', $data, $template);
    }

    // Confirmation present
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (!xarModAPIFunc('publications',
                     'admin',
                     'delete',
                     array('id' => $id,
                           'ptid' => $ptid))) {
        return;
    }

    // Success
    xarSession::setVar('statusmsg', xarML('Publication Deleted'));

    // Return return_url
    if (!empty($return_url)) {
        xarResponseRedirect($return_url);
        return true;
    }

    // Return to the original admin view
    $lastview = xarSession::getVar('Publications.LastView');
    if (isset($lastview)) {
        $lastviewarray = unserialize($lastview);
        if (!empty($lastviewarray['ptid']) && $lastviewarray['ptid'] == $ptid) {
            extract($lastviewarray);
            xarResponseRedirect(xarModURL('publications', 'admin', 'view',
                                          array('ptid' => $ptid,
                                                'catid' => $catid,
                                                'state' => $state,
                                                'startnum' => $startnum)));
            return true;
        }
    }

    xarResponseRedirect(xarModURL('publications', 'admin', 'view',
                                  array('ptid' => $ptid)));

    return true;
}

?>
