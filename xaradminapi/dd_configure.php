<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

function uploads_adminapi_dd_configure($confString = NULL)
{
    // Default to multiple selection
    $multiple = TRUE;
    // Grab the sitewide defaults for the methods
    $methods = array(
            'trusted'  => xarModVars::get('uploads', 'dd.fileupload.trusted')  ? TRUE : FALSE,
            'external' => xarModVars::get('uploads', 'dd.fileupload.external') ? TRUE : FALSE,
            'upload'   => xarModVars::get('uploads', 'dd.fileupload.upload')   ? TRUE : FALSE,
            'stored'   => xarModVars::get('uploads', 'dd.fileupload.stored')   ? TRUE : FALSE
            );
    $basedir = null;
    $importdir = null;

    if (!isset($confString) || empty($confString)) {
        $conf = array();
    } elseif (stristr($confString, ';')) {
        $conf = explode(';', $confString);
    } else {
        $conf = array($confString);
    }
    foreach ($conf as $item) {
        $item = trim($item);
        $check = strtolower(substr($item, 0, 6));

        if ('single' == $check) {
            $multiple = 0;
        } elseif ('basedi' == $check) {
            if (preg_match('/^basedir\((.+)\)$/i', $item, $matches)) {
                $basedir = $matches[1];
            }
        } elseif ('import' == $check) {
            if (preg_match('/^importdir\((.+)\)$/i', $item, $matches)) {
                $importdir = $matches[1];
            }
        } elseif ('method' == $check) {
            $item = strtolower($item);
            if (stristr($item, 'methods')) {
                // if it's the methods, then let's set them up
                preg_match('/^methods\(([^)]*)\)$/i', $item, $parts);

                // if any methods were specified, then we should have at -least-
                // two parts here - otherwise, there will be just the whole item
                // if no methods were specified, use the defaults.
                if (count($parts) <= 1) {
                    continue;
                } elseif (count($parts) == 2) {
                    // reset the methods to nothing
                    // and add only the ones specified
                    $list = explode(',', $parts[1]);
                    foreach ($list as $method) {
                        $method = trim(strtolower($method));

                        // grab the modifier if there was one
                        preg_match('/^(\-|\+)?([a-z0-9_-]*)/i', $method, $matches);
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
                            case 'uploads':
                                $methods['upload'] = $modifier;
                                break;
                            case 'external':
                            case 'extern':
                                $methods['external'] = $modifier;
                                break;
                            case 'trusted':
                            case 'trust':
                                $methods['trusted'] = $modifier;
                                break;
                            case 'stored':
                            case 'store':
                                $methods['stored'] = $modifier;
                                break;
                            default:

                        }
                    }
                }

            }
        }
    }

// FIXME: clean up weird return format
    // return the settings
    $options[0] = $multiple;
    $options[1] = $methods;
    $options[2] = $basedir;
    $options[3] = $importdir;
    $options['multiple']  = $multiple;
    $options['methods']   = $methods;
    $options['basedir']   = $basedir;
    $options['importdir'] = $importdir;

    return $options;

}

?>
