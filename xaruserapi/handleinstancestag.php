<?php

/**
 * Handle <xar:workflow-instances ...> workflow instances tags
 * Format : <xar:workflow-instances/>
 *       or <xar:workflow-instances sort_mode="started_desc" numitems="3" />
 *       or <xar:workflow-instances pId="123" layout="tiny" />
 *       or <xar:workflow-instances template="flat" />
 *
 * @param $args array containing the item that you want to display, or fields
 * @returns string
 * @return the PHP code needed to invoke showstatus() in the BL template
 */
function workflow_userapi_handleinstancestag($args)
{
    $out = "echo xarModAPIFunc('workflow','user','showinstances',
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
