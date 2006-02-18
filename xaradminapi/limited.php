<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
*/
/**
 * Unknown
 * Remark: gestire errore su inserted
 * @todo MichelV <1> Keep this file?
 */
function keywords_adminapi_limited($args)
{
    extract($args);
    if (!xarSecurityCheck('AdminKeywords')) return;
    $invalid = array();
    if (!isset($moduleid) || !is_numeric($moduleid)) {
        $invalid[] = 'moduleid';
    }
    if (!isset($keyword) || !is_string($keyword)) {
        $invalid[] = 'keyword';
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $invalid[] = 'itemtype';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update limited', 'Keywords');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $key = xarModAPIFunc('keywords',
                         'admin',
                         'separekeywords',
                          array('keywords' => $keyword));

    foreach ($key as $keyres) {
    $keyres = trim($keyres);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $keywordstable = $xartable['keywords_restr'];
    $nextId = $dbconn->GenId($keywordstable);
    $query = "INSERT INTO $keywordstable (
              xar_id,
              xar_keyword,
              xar_moduleid,
              xar_itemtype)
              VALUES (
              ?,
              ?,
              ?,
              ?)";
    $result =& $dbconn->Execute($query,array($nextId, $keyres, $moduleid, $itemtype));
    if (!$result) return;
    }
    return;
}
?>
