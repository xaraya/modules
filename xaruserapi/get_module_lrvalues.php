<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Grab the left and right values for each object of a particular module
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $modid     id of the module to gather info on
 * @returns  array an array containing the left and right values or an
 *           empty array if the modid specified doesn't exist
 */
function comments_userapi_get_module_lrvalues( $args )
{

    extract ($args);

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Missing or Invalid parameter \'modid\'!!');
        throw new BadParameterException($msg);
    }

    if (empty($itemtype)) {
        $itemtype = 0;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $sql = "SELECT  objectid AS objectid,
                    MIN(left_id) AS left_id,
                    MAX(right_id) AS right_id
              FROM  $xartable[comments]
             WHERE  modid=$modid
               AND  itemtype=$itemtype
          GROUP BY  objectid";

    $result =& $dbconn->Execute($sql);

    if(!$result)
        return;

    if (!$result->EOF) {
        while (!$result->EOF) {
            $row = $result->GetRowAssoc(false);
            $lrvalues[] = $row;
            $result->MoveNext();
        }
    } else {
        $lrvalues = array();
    }

    $result->Close();

    return $lrvalues;
}

?>
