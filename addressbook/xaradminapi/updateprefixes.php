<?php
/**
 * File: $Id: updateprefixes.php,v 1.5 2003/07/21 21:13:03 garrett Exp $
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
function addressbook_adminapi_updateprefixes($args) {

    // var defines
    $dels = '';

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
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
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
            if(!empty($dels) && count($del)) {
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

    $xarTables =& xarDBGetTables();
    $preTable = $xarTables['addressbook_prefixes'];

    $updates = array();
    foreach($modID as $k=>$id) {
    array_push($updates,"UPDATE $preTable
                            SET name ='".xarVarPrepForStore($modName[$k])."'
                          WHERE nr = $id");
    }

    if(xarModAPIFunc(__ADDRESSBOOK__,'admin','updateitems',array('tablename'=>'prefixes','updates'=>$updates))) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_INFO,
                    new abUserException('UPDATE - '._AB_SUCCESS));
    }

    if(!empty($dels)) {
        $delete = "DELETE FROM $preTable WHERE nr IN ($dels)";
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','deleteitems',array('tablename'=>'prefixes','delete'=>$delete))) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_INFO,
                    new abUserException('DELETE - '._AB_SUCCESS));
        }
    }

    if( (isset($newname)) && ($newname != '') ) {
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','additems',array('tablename'=>'prefixes','name'=>$newname))) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_INFO,
                    new abUserException('INSERT - '._AB_SUCCESS));
        }
    }

    // Return
    return true;

} // END updateprefixes

?>