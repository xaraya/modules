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
 * Modify a blacklist domain pattern
 *
 * @author  Carl P. Corliss (aka rabbitt)
 * @access  public
 * @param    array      $args   Hashed array of arguments with 'name' => 'value' relationships
 * <pre>
 *      <em>mixed  </em> <strong>id      </strong> id of the domain pattern to modify
 *      <em>integer</em> <strong>domain  </strong> the new domain pattern to use for the specified id
 * </pre>
 * @returns bool True if successful, false otherwise
 */
function blacklist_adminapi_modify($args) 
{

    extract($args);

    $msg = xarML('Missing or Invalid Parameters: ');;
    $error = FALSE;

    if (!isset($domain)) {
        $msg .= xarMLbykey('domain ');
        $error = TRUE;
    }

    if (!isset($id)) {
        $msg .= xarMLbykey('id ');
        $error = TRUE;
    }

    if ($error) {
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    $sql =  "UPDATE $xartable[blacklist]
                SET xar_domain = ?
              WHERE xar_id      = ?";

    $bindvars = array($domain, $id);
    $result = &$dbconn->Execute($sql,$bindvars);

    if (!$result) {
        return;
    }

    return true;
}

?>
