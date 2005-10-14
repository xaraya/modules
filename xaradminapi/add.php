<?php

/**
 * File: $Id$
 *
 * BlackList API 
 *
 * @package Modules
 * @copyright (C) 2002-2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BlackList
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
*/

/**
 * Adds a new blacklist domain pattern 
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    array      $args   Hashed array of arguments with 'name' => 'value' relationships
 * <pre>
 *      <em>string  </em> <strong>domain      </strong> Blacklist domain pattern to add
 * </pre>
 * @returns  integer     the id of the new blacklist domain pattern
 */
function blacklist_adminapi_add($args) 
{
    extract($args);

    if (!isset($domain) || empty($domain)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                                 'domain', 'adminapi', 'add', 'blacklist');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $id = $dbconn->GenId($xartable['blacklist']);

    $sql = "INSERT 
              INTO $xartable[blacklist] (xar_id, xar_domain)
            VALUES (?,?)";

    $bindvars = array($id, $domain);
    $result = &$dbconn->Execute($sql,$bindvars);

    if (!$result) {
        return;
    } else {
        $id = $dbconn->PO_Insert_ID($xartable['blacklist'], 'xar_cid');
        return $id;
    }
}
?>
