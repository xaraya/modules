<?php
/**
 * File: $Id: updatecategories.php,v 1.8 2004/01/24 18:36:22 garrett Exp $
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
 * update the categories
 *
 * @param passed in from modifycategories api
 * @return bool
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function addressbook_adminapi_updatecategories($args) 
{

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
    if (!isset($newname)) { $invalid[] = 'newname'; }
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
    $cat_table = $xarTables['addressbook_categories'];

    $updates = array();
    foreach($modID as $k=>$id) {
    array_push($updates,"UPDATE $cat_table
                            SET name ='".xarVarPrepForStore($modName[$k])."'
                          WHERE nr = $id");
    }

    if(xarModAPIFunc(__ADDRESSBOOK__,'admin','updateitems',array('tablename'=>'categories','updates'=>$updates))) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_INFO,
                    new abUserException('UPDATE - '._AB_SUCCESS));
    }

    if(!empty($dels)) {
        $delete = "DELETE FROM $cat_table WHERE nr IN ($dels)";
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','deleteitems',array('tablename'=>'categories','delete'=>$delete))) {
            xarErrorSet(XAR_USER_EXCEPTION,
                        _AB_ERR_INFO,
                        new abUserException('DELETE - '._AB_SUCCESS));
        }
    }

    if( (isset($newname)) && ($newname != '') ) {
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','additems',array('tablename'=>'categories','name'=>$newname))) {
            xarErrorSet(XAR_USER_EXCEPTION,
                        _AB_ERR_INFO,
                        new abUserException('INSERT - '._AB_SUCCESS));
        }
    }

    // Return
    return TRUE;

} // END updatecategories

?>