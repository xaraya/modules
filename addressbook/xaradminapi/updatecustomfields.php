<?php
/**
 * File: $Id: updatecustomfields.php,v 1.6 2003/07/06 04:53:44 garrett Exp $
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
 * update the customfield settings
 *
 * @param passed in from modifycustomfields api
 * @return bool
 * @raise _AB_GLOBALPROTECTERROR, _AB_GRANTERROR, _AB_SORTERROR_1,
 *        _AB_SORTERROR_2, _AB_SPECIAL_CHARS_ERROR
 */
function addressbook_adminapi_updatecustomfields($args) {

    /**
     * Security check
     */
    if (!xarSecurityCheck('AdminAddressBook',0)) return FALSE;

    extract($args);

    $invalid = array();
    if (!isset($id)) { $invalid[] = '*id*'; }
    if (!isset($del)) { $invalid[] = '*del*'; }
    if (!isset($custLabel)) { $invalid[] = '*custLabel*'; }
    if (!isset($custType)) { $invalid[] = '*custType*'; }
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
    $modType = array();
    $modDel = array();
    $modDelType = array();

    /**
     * we can resequence the custom fields OR make data updates
     * not both
     */
    if (isset($incID) && $incID > 0) {
        if (!xarModAPIFunc(__ADDRESSBOOK__, 'admin', 'inccustomfields', array('id' => $incID))) {
            return FALSE;
        }
    } elseif (isset($decID) && $decID > 0) {
        if (!xarModAPIFunc(__ADDRESSBOOK__, 'admin', 'deccustomfields', array('id' => $decID))) {
            return FALSE;
        }
    } else {

        // Update / Insert / Delete a custom field
        if(isset($id)) {
            foreach($id as $k=>$datatype) {
                $found = false;
                if(isset($dels) && count($del)) {
                    foreach($del as $d) {
                        if($datatype == $d) {
                            $found = true;
                            array_push($modDel,$datatype);
                            array_push($modDelType,$custType[$k]);
                            break;
                        }
                    }
                }
                if(!$found) {
                    array_push($modID,$datatype);
                    array_push($modName,$custLabel[$k]);
                    array_push($modType,$custType[$k]);
                }
            }
        }
        $xarTables = xarDBGetTables();
        $cus_table = $xarTables['addressbook_customfields'];
        $adr_table = $xarTables['addressbook_address'];

        $updates = array();

        foreach($modID as $k=>$id) {
            array_push($updates,"UPDATE $cus_table
                                    SET label='".xarVarPrepForStore($modName[$k])."',
                                        type='".xarVarPrepForStore($modType[$k])."'
                                  WHERE nr=$id");
            if (($modType[$k] != 'smallint default NULL') && ($modType[$k] != 'tinyint default NULL')) {
                array_push($updates,"ALTER TABLE $adr_table CHANGE custom_".$id." custom_".$id." ".xarVarPrepForStore($modType[$k]));
            }
        }

        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','updateitems',array('tablename'=>'customfields','updates'=>$updates))) {
            xarErrorSet(XAR_USER_EXCEPTION,
                        _AB_ERR_INFO,
                        new abUserException('UPDATE - '._AB_SUCCESS));
        } else {
            return FALSE;
        }

        if (count($modDel)) {
            if(xarModAPIFunc(__ADDRESSBOOK__,'admin','deletecustomfields',array('modDel'=>$modDel,'modDelType'=>$modDelType))) {
                xarErrorSet(XAR_USER_EXCEPTION,
                            _AB_ERR_INFO,
                            new abUserException('DELETE - '._AB_SUCCESS));
                if (!xarModAPIFunc(__ADDRESSBOOK__,'admin','resequencecustomfields')) {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }
        if (isset($newtype) && ($newtype == 'tinyint default NULL')) {
            $newname = '[      ]';
        }
        if (isset($newtype) && ($newtype == 'smallint default NULL')) {
            $newname = '[------]';
        }
        if( (isset($newname)) && ($newname != '') ) {
            $dbconn =& xarDBGetConn();
            $result = $dbconn->Execute("SELECT MAX(nr) FROM $cus_table");
            list($nextID) = $result->fields;
            $nextID++;
            $result->Close();
            $inserts = array();
            array_push($inserts,"INSERT INTO $cus_table (nr,label,type,position)
                                  VALUES ($nextID,'".xarVarPrepForStore($newname)."','".xarVarPrepForStore($newtype)."',9999999999)");
            if (($newtype != 'smallint default NULL') && ($newtype != 'tinyint default NULL')) {
                array_push($inserts,"ALTER TABLE $adr_table ADD custom_".$nextID." ".xarVarPrepForStore($newtype));
            }

            if(xarModAPIFunc(__ADDRESSBOOK__,'admin','addcustomfields',array('inserts'=>$inserts))) {
                xarErrorSet(XAR_USER_EXCEPTION,
                            _AB_ERR_INFO,
                            new abUserException('INSERT - '._AB_SUCCESS));
                if (!xarModAPIFunc(__ADDRESSBOOK__,'admin','resequencecustomfields')) {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } // END insert / update / delete

    } // end IF inc / dec field position

    return TRUE;
}

?>