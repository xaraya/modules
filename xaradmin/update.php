<?php
/**
 * This is a standard function that is called with the results of the
 * form supplied by sitecloud_admin_modify() to update a current item
 * @param 'id' the id of the link to be updated
 * @param 'url' the url of the link to be updated
 * @param 'title' the title of the link to be updated
 */
function sitecloud_admin_update()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('id','int:1:',$id)) return;
    if (!xarVarFetch('obid','str:1:',$obid,$id,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title','str:1:',$title,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('url','str:1:',$url,'http://www.xaraya.com',XARVAR_NOT_REQUIRED)) return;
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('sitecloud',
                      'admin',
                      'update',
                      array('id'   => $id,
                            'title' => $title,
                            'url'   => $url))) return;
    xarResponseRedirect(xarModURL('sitecloud', 'admin', 'view'));
    // Return
    return true;
}
?>
