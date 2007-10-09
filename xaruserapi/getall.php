<?php
/**
 * Getall responses
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Get all response items
 *
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function sitecontact_userapi_getall($args)
{
    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

   $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'Sitecontact');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    if (!xarSecurityCheck('EditSitecontact')) return; //this is higher than normal as normal visitors don't get to see these

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $sitecontactResponseTable = $xartable['sitecontact_response'];
    
    $bindvars=array();
    
   if (!empty($scrid)) {
        $where = "WHERE xar_scrid = ?";
        $bindvars[] = $scrid;
    } else {
        $wherelist = array();
        $fieldlist = array('scid','responsetime','username','useremail','company','useripaddress');
        foreach ($fieldlist as $field) {
            if (isset($$field)) {
                $wherelist[] = "xar_$field = ?";
                $bindvars[] = $$field;
            }
        }
        if (count($wherelist) > 0) {
            $where = "WHERE " . join(' AND ',$wherelist);
        } else {
            $where = '';
        }
    }
    $query = "SELECT xar_scrid,
                     xar_scid,
                     xar_username,
                     xar_useremail,
                     xar_requesttext,
                     xar_company,
                     xar_usermessage,
                     xar_useripaddress,
                     xar_userreferer,
                     xar_sendcopy,
                     xar_permission,
                     xar_bccrecipients,
                     xar_ccrecipients,
                     xar_responsetime
                     FROM $sitecontactResponseTable
                    $where
                    ORDER BY xar_scrid";
    if (!empty($scrid)) {
        $result =& $dbconn->Execute($query,$bindvars);
    } else {
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1,$bindvars );
    }

    if ($result->EOF) {
        return false;
    }

   if (isset($scid)) {
     $thistype= $scid;
   }else {
     $thistype=xarModGetVar('sitecontact','defaultform');
   }
 
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1,$bindvars );

    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($scrid,$scid, $username,$useremail,$requesttext,$company,$usermessage,$useripaddress,
             $userreferer, $sendcopy,$permission, $bccrecipients, $ccrecipients, $responsetime) = $result->fields;
        if (xarSecurityCheck('ViewSitecontact', 0, 'sitecontact', "$scid:All:$scrid")) {
            $items[] =array('scrid'         => (int)$scrid,
                            'scid'          => (int)$scid,
                            'username'      => $username,
                            'useremail'     => $useremail,
                            'requesttext'   => $requesttext,
                            'company'       => $company,
                            'usermessage'   => $usermessage,
                            'useripaddress' => $useripaddress,
                            'userreferer'   => $userreferer, 
                            'sendcopy'      => $sendcopy,
                            'permission'    => $permission, 
                            'bccrecipients' => $bccrecipients, 
                            'ccrecipients'  => $ccrecipients, 
                            'responsetime'  => $responsetime
                            );
        }
    }
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the items */
    return $items;
}
?>