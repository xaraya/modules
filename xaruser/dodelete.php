<?php
/**
 * AddressBook user doDelete
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
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
    $menuValues = xarModAPIFunc('addressbook','user','getmenuvalues');
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
        $output['infoMsg'] = xarML('Not authorised to access the AddressBook module.');
        $output['backToListTEXT'] = xarML('Back to list');
        return $output;
    }

    if(!xarModAPIFunc('addressbook','user','deleterecord',array('id'=>$id))) {
        $output = array();
        $output['infoMsg'] = xarML('Deletion of this record failed. Please contact your administrator!');
        $output['backToListTEXT'] = xarML('Back to list');
        return $output;
    }

    // This function generated no output
    xarResponseRedirect(xarModURL('addressbook', 'user', 'viewall',$output['menuValues']));

    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        // Got an exception
        if ((xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION) && !_AB_DEBUG) {
            return; // throw back
        } else {
                        // We are going to handle this exception REGARDLESS of the type
            $output['abExceptions'] = xarModAPIFunc('addressbook','user','handleexception');
        }
    }

    // Return
    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

} // END doDelete

?>
