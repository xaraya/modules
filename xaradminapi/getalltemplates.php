<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * Get the name of all templates
 *
 * @return array of templates ids and names
 */
function pubsub_adminapi_getalltemplates($args)
{
    $templates = array();
    if (!xarSecurityCheck('AdminPubSub')) return;

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    $query = "SELECT xar_templateid,
                     xar_name
                FROM $pubsubtemplatestable";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($templateid, $name) = $result->fields;
        $templates[$templateid] = $name;
    }

    $result->Close();

    return $templates;
}

?>
