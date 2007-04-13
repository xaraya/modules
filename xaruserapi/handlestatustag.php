<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * Handle <xar:workflow-status ...> workflow status tags
 * Format : <xar:workflow-status/>
 *       or <xar:workflow-status status="active" />
 *       or <xar:workflow-status id="123" layout="tiny" />
 *       or <xar:workflow-status template="flat" />
 *
 * @param $args array containing the item that you want to display, or fields
 * @return string the PHP code needed to invoke showstatus() in the BL template
 */
function workflow_userapi_handlestatustag($args)
{
    $out = "echo xarModAPIFunc('workflow','user','showstatus',
                   array(\n";
    if (empty($args)) $args = array();
    foreach ($args as $key => $val) {
        if (is_numeric($val) || substr($val,0,1) == '$') {
            $out .= "                         '$key' => $val,\n";
        } else {
            $out .= "                         '$key' => '$val',\n";
        }
    }
    $out .= "                         ));";
    return $out;
}

?>
