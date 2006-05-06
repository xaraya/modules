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
 * Handle <xar:workflow-activity ...> workflow activity tags
 * Format : <xar:workflow-activity activityId="456" instanceId="123" productid="whatever" .../>
 *
 * @param $args array containing the item that you want to display, or fields
 * @return string The PHP code needed to invoke showactivity() in the BL template
 */
function workflow_userapi_handleactivitytag($args)
{
    $out = "echo xarModAPIFunc('workflow',
                   'user',
                   'showactivity',
                   array(\n";
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
