<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
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

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
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
