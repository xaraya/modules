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

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $keywordstable = $xartable['keywords_restr'];
    $nextId = $dbconn->GenId($keywordstable);
    $query = "INSERT INTO $keywordstable (
              id,
              keyword,
              module_id,
              itemtype)
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
