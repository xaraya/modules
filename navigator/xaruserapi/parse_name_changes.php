<?php

function navigator_userapi_parse_name_changes( $args ) {

    extract($args);

    if (!isset($names) || empty($names)) {
        return array();
    }

    if (!isset($base)) {
        return array();
    }

    $current_cids = xarModAPIFunc('navigator', 'user', 'get_current_cats');

    if (xarModGetVar('navigator', 'style.matrix')) {
        $matrix = TRUE;
    } else {
        $matrix = FALSE;
    }

    if (empty($current_cids)) {
        return array();
    } else {
        extract($current_cids);
    }

    $renameList = array();

    $tmpList = explode('|', $names);

    if (count($tmpList)) {

        foreach ($tmpList as $item) {

            $tmp = explode('+', $item);
            if ((!isset($tmp[1]) || !isset($tmp[0])) ||
                    empty($tmp[1]) || !is_numeric($tmp[0])) {
                    // skip any that are incorrectly formatted
                    continue;
            } else {

                $id = $tmp[0];

                if ($tmp[1] == '@') {
                    switch (strtolower($base)) {
                        case 'secondary':
                            $name = $primary['name'];
                            break;
                        case 'primary':
                            if ($matrix && isset($secondary) && count($secondary)) {
                                $name = $secondary['name'];
                            } else {
                                // Here we back up out of the switch
                                // and continue in the foreach
                                continue 2;
                            }
                            break;
                    }
                } else {
                    $name = $tmp[1];
                }

                $renameList[$id] = $name;
            }
        }
    }

    return $renameList;
}
?>