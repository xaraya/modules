<?php
/**
 * File: $Id: updateprefixes.php,v 1.3 2003/07/05 23:08:23 garrett Exp $
 *
 * AddressBook admin functions
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

/**
 * update the prefix fields
 *
 * @param passed in from modifylabels api
 * @return bool
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function AddressBook_adminapi_updateprefixes($args) {

	/**
	 * Security check 
	 */
    if (!xarSecurityCheck('AdminAddressBook',0)) return FALSE;

	extract($args);

    $invalid = array();
	if (!isset($id)) { $invalid[] = 'id'; } 
	if (!isset($del)) { $invalid[] = 'del'; } 
	if (!isset($name)) { $invalid[] = 'name'; } 
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     join(', ',$invalid), 'updatelabels', __ADDRESSBOOK__);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return FALSE;
    }

    if(is_array($del)) {
        $dels = implode(',',$del);
    }
    $modID = $modName = array();

    if(isset($id) && is_array($id)) {
        foreach($id as $k=>$i) {
            $found = false;
            if(isset($dels) && count($del)) {
                foreach($del as $d) {
                    if($i == $d) {
                        $found = true;
                        break;
                    }
                }
            }
            if(!$found) {
                array_push($modID,$i);
                array_push($modName,$name[$k]);
            }
        }
    }

    $xarTables = xarDBGetTables();
    $preTable = $xarTables['addressbook_prefixes'];

    $updates = array();
    foreach($modID as $k=>$id) {
    array_push($updates,"UPDATE $preTable
                            SET name ='".xarVarPrepForStore($modName[$k])."'
                          WHERE nr = $id");
    }

    if(xarModAPIFunc(__ADDRESSBOOK__,'admin','updateItems',array('tablename'=>'prefixes','updates'=>$updates))) {
//FIXME: <garrett> we want to say SUCCESS while at the same time
//		printing additional informational messages. how can we
//      prioritize them as done here? 
//    $msg = xarVarPrepHTMLDisplay();
//    if (isset($error)) { $msg .= ' - '.$error; }
		xarExceptionSet(XAR_USER_EXCEPTION, 
						_AB_ERR_DEBUG, 
						new abUserException('UPDATE - '._AB_SUCCESS));
// END FIXME
    }

    if(isset($dels)) {
        $delete = "DELETE FROM $preTable WHERE nr IN ($dels)";
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','deleteItems',array('tablename'=>'prefixes','delete'=>$delete))) {
//FIXME: <garrett> we want to say SUCCESS while at the same time
//		printing additional informational messages. how can we
//      prioritize them as done here? 
//    $msg = xarVarPrepHTMLDisplay();
//    if (isset($error)) { $msg .= ' - '.$error; }
		xarExceptionSet(XAR_USER_EXCEPTION, 
						_AB_ERR_DEBUG, 
						new abUserException('DELETE - '._AB_SUCCESS));
// END FIXME
        }
    }

    if( (isset($newname)) && ($newname != '') ) {
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','addItems',array('tablename'=>'prefixes','name'=>$newname))) {
//FIXME: <garrett> we want to say SUCCESS while at the same time
//		printing additional informational messages. how can we
//      prioritize them as done here? 
//    $msg = xarVarPrepHTMLDisplay();
//    if (isset($error)) { $msg .= ' - '.$error; }
		xarExceptionSet(XAR_USER_EXCEPTION, 
						_AB_ERR_DEBUG, 
						new abUserException('INSERT - '._AB_SUCCESS));
// END FIXME
        }
    }

    // Return
    return true;

} // END updateprefixes

?>