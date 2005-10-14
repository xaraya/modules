<?php

/**
 * File: $Id$
 *
 * isBlackListed function
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
 * Verifies whether a domain is blacklisted or not
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access public
 * @param string $domain Domain to verify for blacklist status
 * @returns bool True if blacklisted, false otherwise
 *
 */
function blacklist_userapi_is_blacklisted( $domain )
{
	$isBlackListed = FALSE;

	$domain = trim($domain);
	if (empty($domain)) {
		return $isBlackListed;
	}

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $blTable = $xartable['blacklist'];

	$query = "SELECT xar_domain AS pattern 
				FROM $blTable";
	$patterns =& $dbconn->Execute($query);	

    if(!$patterns) {
        return;
	} else {
        $domainTest = trim(preg_replace($patterns, '', $domain));

        if (empty($domainTest)) {
            $isBlackListed = TRUE;
		}
	}

	return $isBlackListed;
}
?>
