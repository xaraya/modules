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
 * Grab the left and right values for a particular node
 * (aka comment) in the database
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $id     id of the comment to lookup
 * @returns  array an array containing the left and right values or an
 *           empty array if the comment_id specified doesn't exist
 */
function comments_userapi_get_node_lrvalues( $args )
{

    extract( $args );

    if (empty($id)) {
        $msg = xarML('Missing or Invalid parameter \'id\'!!');
        throw new BadParameterException($msg);
    }

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    $sql = "SELECT  left_id, right_id
              FROM  $xartable[comments]
             WHERE  id=$id";

    $result =& $dbconn->Execute($sql);

    if(!$result)
        return;

    if (!$result->EOF) {
        $lrvalues = $result->GetRowAssoc(false);
    } else {
        $lrvalues = array();
    }

    $result->Close();

    return $lrvalues;
}

?>
