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
 * get an existing pubsub template
 * @param $args['templateid'] the ID of the item
 * @returns array
 * @return array of template information
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_gettemplate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($templateid) || !is_numeric($templateid)) {
        $invalid[] = 'templateid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for function #(2)() in module #(3)',
                    join(', ',$invalid), 'gettemplate', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', "All:All:All:$templateid")) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    // Update the item
    $query = "SELECT xar_templateid,
                     xar_name,
                     xar_template,
                     xar_compiled
              FROM $pubsubtemplatestable
              WHERE xar_templateid = ?";
    $result = $dbconn->Execute($query, array((int)$templateid));
    if (!$result) return;

    $info = array();
    if ($result->EOF) return $info;

    list($info['templateid'],$info['name'],$info['template'],$info['compiled']) = $result->fields;
    $result->Close();

    return $info;
}

?>
