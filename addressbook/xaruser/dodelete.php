<?php
/**
 * File: $Id: dodelete.php,v 1.3 2003/12/22 07:12:50 garrett Exp $
 *
 * AddressBook user doDelete
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

//FIXME: <garrett> why is this not an API?

//=========================================================================
//  Delete a record
//=========================================================================
function addressbook_user_dodelete() 
{
    if (!xarVarFetch ('id','int::',$id, FALSE)) return FALSE;

    // save menu settings
    $menuValues = xarModAPIFunc(__ADDRESSBOOK__,'user','getmenuvalues');
    foreach ($menuValues as $key=>$value) {
        $output[$key] = $value;
    }

    $output['menuValues']=array('catview'   =>$output['catview']
                    ,'menuprivate'=>$output['menuprivate']
                    ,'all'       =>$output['all']
                    ,'sortview'  =>$output['sortview']
                    ,'page'      =>$output['page']
                    ,'char'      =>$output['char']
                    );

    // Security check
    // Confirm authorisation code
    //if (!pnSecAuthAction(0, 'AddressBook::', '::', ACCESS_READ)) {
    if (!xarSecConfirmAuthKey()) {
        $output = array();
        $output['infoMsg'] = xarML(_ADDRESSBOOK_NOAUTH);
        $output['backToListTEXT'] = xarML(_AB_GOBACK);
        return $output;
    }

    if(!xarModAPIFunc(__ADDRESSBOOK__,'user','deleterecord',array('id'=>$id))) {
        $output = array();
        $output['infoMsg'] = xarML(_AB_DELETENOSUCCESS);
        $output['backToListTEXT'] = xarML(_AB_GOBACK);
        return $output;
    }

    // This function generated no output
    xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'user', 'viewall',$output['menuValues']));

    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        // Got an exception
        if ((xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION) && !_AB_DEBUG) {
            return; // throw back
        } else {
                        // We are going to handle this exception REGARDLESS of the type
            $output['abExceptions'] = xarModAPIFunc(__ADDRESSBOOK__,'user','handleexception');
        }
    }

    // Return
    return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));

} // END doDelete

?>
