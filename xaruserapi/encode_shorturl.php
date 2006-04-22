<?php
/**
 * Return the path for a short URL to xarModURL for this module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * return the path for a short URL to xarModURL for this module
 *
 * Supported URLs :
 *
 * /registration/privacy
 * /registration/terms
 *
 * /registration/register
 * /registration/checkage
 * /registration
 * @author the roles module development team
 * @param $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function registration_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);
    if (!isset($func)) {return;}

    $path = array();
    $get = $args;
    $module = 'registration';

    $aliasisset = xarModGetVar($module, 'useModuleAlias');
    $aliasname = xarModGetVar($module, 'aliasname');

    if (!empty($aliasisset) && !empty($aliasname)) {
        $module_for_alias = xarModGetAlias($aliasname);

        if ($module_for_alias == $module) {
            $module = $aliasname;
        }
    }

    $path[] = $module;

    if ($func == 'main') {
         $path[] = 'main';
        // Consume the 'func' parameter only.
        unset($get['func']);
    } elseif ($func == 'terms' || $func == 'privacy') {
        $path[] = $func;
        unset($get['func']);

    } elseif ($func == 'register') {
            $path[] = 'register';
            if (!empty($phase)) {
                // Bug 4404: registerform and registration are aliases.
                if ($phase == 'registerform' || $phase == 'registration' || $phase == 'checkage') {
                    unset($args['phase']);
                    $path[] = $phase=='checkage'?'checkage':'';//($phase == 'registerform' ? 'registration' : $phase);
                } else {
                    // unsupported phase - must be passed via forms
                }
            }
    } else {
        //hmmm
    }
    return array('path' => $path, 'get' => $get);
}
?>