<?php
/**
 * Get responses
 *
 * @package Xaraya
 * @copyright (C) 2004-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Xarigami SiteContact Module
 * @copyright (C) 2007,2008 2skies.com
 * @link http://xarigami.com/project/sitecontact
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
function sitecontact_userapi_get($args)
{
    extract($args);

    // Argument check
    if (isset($scrid) && (!is_numeric($scrid) || $scrid < 1)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Sitecontact RID', 'user', 'get',
                    'Sitecontact');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
    
    

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
                    $where";
    if (!empty($scrid)) {
        $result =& $dbconn->Execute($query,$bindvars);
    } else {
        $result =& $dbconn->SelectLimit($query,1,0,$bindvars);
    }
    if (!$result) return;

    if ($result->EOF) {
        return false;
    }

    list($scrid,$scid, $username,$useremail,$requesttext,$company,$usermessage,$useripaddress,
         $userreferer, $sendcopy,$permission, $bccrecipients, $ccrecipients, 
         $responsetime) = $result->fields;
         
    if (xarSecurityCheck('ViewSitecontact', 0, 'sitecontact', "$scid:All:$scrid")) {
            $response =array('scrid'         => (int)$scrid,
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
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the items */
    return $response;
}
?>