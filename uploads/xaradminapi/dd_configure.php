<?php


function uploads_adminapi_dd_configure($confString = NULL)
{

    $multiple = TRUE;
    // Default stored is on
    $methods = array('stored'   => NULL,
                     'upload'   => NULL,
                     'trusted'  => NULL,
                     'external' => NULL);
    $style = 'icon';
    $basedir = '';

    if (empty($confString)) {
        return array('multiple' => $multiple,
                     'methods'  => $methods,
                     'output'   => $output);
    }

    if (stristr($confString, ':')) {
        $conf = explode(':', $confString);
    } else {
        $conf = array($confString);
    }

    foreach ($conf as $item) {

        $item = strtolower(trim($item));
        $check = substr($item, 0, 6);

        switch (strtolower($check)) {
            case 'single':
                $multiple = FALSE;
                break;
            case 'style=':
                $pos = strpos($item, '=');

                if ($pos === FALSE) {
                    $style = 'icon';
                } else {
                    $value = substr($item, $pos + 1);

                    switch ($value) {
                        case 'raw':
                            $style = 'raw';
                            break;
                        case 'link':
                            $style = 'link';
                            break;
                        case 'icon':
                        default:
                            $style = 'icon';
                            break;
                    }
                }
                break;
            case 'method':
                if (stristr(strtolower($item), 'methods')) {
                    // if it's the methods, then let's set them up
                    eregi('^methods\(([^)]*)\)$', $item, $parts);

                    // if any methods were specified, then we should have at -least-
                    // two parts here - otherwise, there will be just the whole item
                    // if no methods were specified, default to stored
                    if (count($parts) <= 1) {
                        $methods = array('stored' => TRUE);
                    } elseif (count($parts) == 2) {
                        // reset the methods to nothing
                        // and add only the ones specified
                        $methods = explode(',', $parts[1]);

                        foreach ($methods as $method) {
                            $method = trim(strtolower($method));

                            // grab the modifier if there was one
                            eregi('^(\-|\+)?([a-z0-9_-]*)', $method, $matches);
                            list($full, $modifier, $method) = $matches;
                            // If modifier == '-' then we are specifically
                            // turning off this file import method,
                            // otherwise, leave it as on
                            if (!empty($modifier) && $modifier == '-') {
                                $modifier = (int) FALSE;
                            } else {
                                $modifier = (int) TRUE;
                            }

                            switch ($method) {
                                case 'upload':
                                    $methods['upload'] = $modifier;
                                    break;
                                case 'external':
                                    $methods['external'] = $modifier;
                                    break;
                                case 'trusted':
                                    $methods['trusted'] = $modifier;
                                    break;
                                case 'stored':
                                    $methods['stored'] = $modifier;
                                    break;
                                default:

                            }
                        }
                    }
                }
                break;
            default:
                // if it's a path, see if it exists and, if so, make it our basedir
                if (file_exists(rtrim($item))) {
                    $basedir = rtrim($item);
                } else {
                    }
                break;
        }
    }

    // return the settings
    return array('multiple' => $multiple,
                 'methods'  => $methods,
                 'output'   => $output
                 'basedir'  => $basedir);

}

?>