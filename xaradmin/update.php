<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * This is a standard function that is called with the results of the
 * form supplied by headlines_admin_modify() to update a current item
 * @param 'hid' the id of the link to be updated
 * @param 'url' the url of the link to be updated
 */
function headlines_admin_update()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('hid','int:1:',$hid)) return;
    if (!xarVarFetch('obid','str:1:',$obid,$hid,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title','str:1:',$data['title'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('desc','str:1:',$data['desc'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order','str:1:',$order,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('url','str:1:',$url,'http://www.xaraya.com/?theme=rss',XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    $data['transform'] = array('desc');
    $data = xarModCallHooks('item', 'transform-input', 0, $data, 'headlines', 0);

    if(!xarModAPIFunc('headlines',
                      'admin',
                      'update',
                      array('hid'   => $hid,
                            'title' => $data['title'],
                            'desc'  => $data['desc'],
                            'url'   => $url,
                            'order' => $order))) return;

    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));

    // Return
    return true;
}
?>