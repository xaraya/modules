<?
/**
 * This is a standard function that is called with the results of the
 * form supplied by headlines_admin_modify() to update a current item
 * @param 'hid' the id of the link to be updated
 * @param 'url' the url of the link to be updated
 */
function headlines_admin_update($args)
{
    // Get parameters from whatever input we need
    list($hid,
         $obid,
         $title,
         $desc,
         $order,
         $url) = xarVarCleanFromInput('hid',
                                      'obid',
                                      'title',
                                      'desc',
                                      'order',
                                      'url');

    extract($args);

    if (!empty($obid)) {
        $hid = $onid;
    }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if(!xarModAPIFunc('headlines',
                      'admin',
                      'update',
                      array('hid'   => $hid,
                            'title' => $title,
                            'desc'  => $desc,
                            'url'   => $url,
                            'order' => $order))) return;

    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));

    // Return
    return true;
}
?>