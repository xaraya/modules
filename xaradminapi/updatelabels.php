<?php
/**
 * AddressBook admin function
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
/**
 * update the label information used in the contact form
 *
 * @param passed in from modifylabels api
 * @return bool
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function addressbook_adminapi_updatelabels($args)
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
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     join(', ',$invalid), 'updatelabels', 'addressbook');
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
    $labelTable = $xarTables['addressbook_labels'];

    $updates = array();
    foreach($modID as $k=>$id) {
    array_push($updates,array('sql'=>"UPDATE $labelTable
                                         SET name = ?
                                       WHERE nr = ?"
                              ,'bindvars'=>array($modName[$k],$id)));
    }

    if(xarModAPIFunc('addressbook','admin','updateitems',array('tablename'=>'labels','updates'=>$updates))) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_INFO,
                    new abUserException('UPDATE - '.xarML('successful')));
    }

    if(!empty($dels)) {
        $delete = "DELETE FROM $labelTable WHERE nr IN ($dels)";
        if(xarModAPIFunc('addressbook','admin','deleteitems',array('tablename'=>'labels','delete'=>$delete))) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_INFO,
                    new abUserException('DELETE - '.xarML('successful')));
        }
    }

    if( (isset($newname)) && ($newname != '') ) {
        if(xarModAPIFunc('addressbook','admin','additems',array('tablename'=>'labels','name'=>$newname))) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_INFO,
                    new abUserException('INSERT - '.xarML('successful')));
        }
    }

    // Return
    return TRUE;

} // END updatelabels

?>
