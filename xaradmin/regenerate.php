<?php

/**
 * Regenerate a simplified list of timezones and DST rules for the base module
 */
function timezone_admin_regenerate()
{
    include 'modules/timezone/tzdata.php';
    $output = '<pre>';

    ksort($Rules);
    $keeprules = array();
    foreach ($Rules as $name => $info) {
        foreach ($info as $id => $data) {
            // keep the current/future rules
            if ($data[1] == 'max') {
                $keeprules[$name][$id] = 1;

            // skip all outdated rules
            } elseif ($data[1] == 'only' && $data[0] < '2005') {

            // skip all other rules too
            } elseif ($data[1] > '2002') {
                //$keeprules[$name][$id] = 1;

            } else {
            }
        }
    }

    $output .= "modules/base/xaruserapi/timezones.php :\n\n";

    $seenrules = array();
    ksort($Zones);
    foreach ($Zones as $name => $info) {
        // skip generic timezones
        if (!strstr($name,'/') || strstr($name,'Etc/')) continue;
        foreach ($info as $id => $data) {
            // skip all outdated entries
            if (isset($data[3]) && $data[3] < '2005') {

            } else {
                $output .= '    $'."Zones['$name'] = array('" . join("', '",$data) . "');\n";
                if ($data[1] != '-' && !isset($keeprules[$data[1]])) {
                    // add missing rules
                    $keeprules[$data[1]] = array();
                }
                $seenrules[$data[1]] = 1;
            }
        }
    }

    $output .= "\nmodules/base/xaruserapi/dtsrules.php :\n\n";

    ksort($keeprules);
    foreach ($keeprules as $rule => $idlist) {
        if (!isset($seenrules[$rule])) {
            $output .= "    // unused in timezones.php\n";
        }
        if (!empty($idlist)) {
            $output .= '    $' . "Rules['$rule'] = array(\n";
            foreach (array_keys($idlist) as $id) {
                $output .= "        array('" . join("', '",$Rules[$rule][$id]) . "'),\n";
            }
            $output .= "    );\n";
        } else {
            $output .= '    $'."Rules['$rule'] = array();\n";
        }
    }

    $output .= '</pre>';
    return $output;
}
?>
