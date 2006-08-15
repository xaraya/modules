<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author the Comments module development team
 * @param $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function comments_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = array();

    // Analyse the different parts of the virtual path
    // $params[1] contains the first part after index.php/example

    // In general, you should be strict in encoding URLs, but as liberal
    // as possible in trying to decode them...

    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('main', $args);

    } elseif (preg_match('/^(\d+)/',$params[1],$matches)) {
        // something that starts with a number must be for the display function
        // Note : make sure your encoding/decoding is consistent ! :-)
        $cid = $matches[1];
        $args['cid'] = $cid;
        return array('display', $args);

/* to be reviewed - probably better to redirect to the module item itself if we know all this
    } elseif (preg_match('/^(\w+)/',$params[1],$matches)) {
        // something that starts with a name might be for the display function
        // Note : make sure your encoding/decoding is consistent ! :-)
        $modname = $matches[1];
        $alias = xarModGetAlias($modname);
        if ($modname != $alias) {
            $itemtype = 0;
            // try to figure out which itemtype we're dealing with
            $itemtypes = xarModAPIFunc($alias,'user','getitemtypes',
                                       array(),0);
            if (!empty($itemtypes) && count($itemtypes) > 0) {
                foreach ($itemtypes as $id => $info) {
                    if (!empty($info['name']) && $modname == $info['name']) {
                        $itemtype = $id;
                        break;
                    }
                }
            }
            $modname = $alias;
        } else {
            $itemtype = 0;
        }
        if (xarModIsAvailable($modname)) {
            $modid = xarModGetIDFromName($modname);
            if (!empty($modid)) {
                $args['modid'] = $modid;
                if (preg_match('/^(\d+)/',$params[2],$matches)) {
                    $temp = $matches[1];
                    if (empty($params[3])) {
                        $args['itemtype'] = $itemtype;
                        $args['objectid'] = $temp;
                        return array('display', $args);
                    } elseif (preg_match('/^(\d+)/',$params[3],$matches)) {
                        $args['itemtype'] = $temp;
                        $args['objectid'] = $matches[1];
                        return array('display', $args);
                    }
                }
            }
        }
*/

    } else {
        // we have no idea what this virtual path could be, so we'll just
        // forget about trying to decode this thing

        // you *could* return the main function here if you want to
        // return array('main', $args);
    }

    // default : return nothing -> no short URL decoded
}

?>