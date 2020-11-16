<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * @todo MichelV what is this?
 * @todo ? inserire controllo sicurezza
 */
function keywords_adminapi_resetlimited($args)
{
    //if (!xarSecurityCheck('AdminKeywords')) return;
    if (!xarSecurityCheck('AddKeywords')) {
        return;
    }
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $keywordstable = $xartable['keywords_restr'];
    $query = "DELETE FROM $keywordstable";
    $result =& $dbconn->Execute($query);
    if (!$result) {
        return;
    }
    return true;
}
