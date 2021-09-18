<?php
/**
 * Extract function and arguments from short URLs for this module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */

/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author jojodee
 * @param  $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function release_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = [];
    $exttypes = xarMod::apiFunc('release', 'user', 'getexttypes');
    $flipext = array_flip($exttypes);
    $extnamearray =[];
    foreach ($exttypes as $k=>$v) {
        $extnamearray[] = strtolower($v);
    }

    if (isset($params[1]) && ($params[1] == 'eid')) {
        if (is_numeric($params[2])) {
            $args['eid'] = (int) $params[2];
            return ['display', $args];
        } else {
            //Lots of hits for categories... But how can we discover them
            //as what we know about them is c107 or c31? Time for Good Urls instead of Short?
            return ['view', $args];
        }
    } elseif (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return ['main', $args];
    } elseif (preg_match('/^index/i', $params[1])) {
        // some search engine/someone tried using index.html (or similar)
        // -> we'll go to the main function
        return ['main', $args];
    } elseif (preg_match('/^viewnotes/i', $params[1])) {
        return ['viewnotes', $args];
    } elseif (preg_match('/^view/i', $params[1])) {
        if (!empty($params[2]) && ($params[2] == 'id.html')) {
            $args['sort']='id';
        } elseif (!empty($params[2]) && ($params[2] == 'name.html')) {
            $args['sort']='name';
        } elseif (!empty($params[2]) && ($params[2] == 'author.html')) {
            $args['sort']='author';
        } elseif (!empty($params[2]) && ($params[2] == 'rstate.html')) {
            $args['sort']='rstate';
        }
        return ['view', $args];
    } elseif (preg_match('/^addid/i', $params[1])) {
        return ['addid', $args];
    } elseif (preg_match('/^displaynote/i', $params[1])) {
        if (!empty($params[2]) && preg_match('/^(\d+)/', $params[2], $matches)) {
            $args['rnid']=$matches[1];
        }
        return ['displaynote', $args];
    } elseif (preg_match('/^addnotes/i', $params[1])) {
        if (empty($params[2])) {
            return ['addnotes', $args];
        } elseif (!empty($params[2]) && ($params[2] == 'start')) {
            $args['phase']='start';
        }
        if (!empty($params[2]) && in_array($params[2], $extnamearray)) {
            if (!empty($params[3]) && preg_match('/^(\d+)/', $params[3], $matches)) {
                $args['rid']=$matches[1];
                $args['exttype'] =(int)array_search($params[2], $flipext);
            }
        } elseif (!empty($params[2]) && preg_match('/^eid/i', $params[2]) && preg_match('/^(\d+)/', $params[3], $matches)) {
            $eid = $matches[1];
            $args['eid'] = (int)$eid;
        } elseif (!empty($params[2]) && preg_match('/^(\d+)/', $params[2], $matches)) {
            $args['rid']=$matches[1];
            $args['exttype']= 1;//try module?
        }

        return ['addnotes', $args];
    } elseif ($params[1] == 'modifyid') {
        if (!empty($params[2]) && in_array($params[2], $extnamearray)) {//try eid first
            if (!empty($params[3]) && preg_match('/^(\d+)/', $params[3], $matches)) {
                $args['rid']=$matches[1];
                $args['exttype'] =(int)array_search($params[2], $flipext);
            }
        } elseif (!empty($params[2]) && preg_match('/^eid/i', $params[2]) && preg_match('/^(\d+)/', $params[3], $matches)) {
            $eid = $matches[1];
            $args['eid'] = (int)$eid;
        } elseif (!empty($params[2]) && preg_match('/^(\d+)/', $params[2], $matches)) {
            $args['rid']=$matches[1];
            $args['exttype']= 1;//try module?
        }
        return ['modifyid', $args];
    } elseif ($params[1] == 'version') {
        if (!empty($params[2]) && in_array($params[2], $extnamearray)) {//try eid first
            if (!empty($params[3]) && preg_match('/^(\d+)/', $params[3], $matches)) {
                // something that starts with a number must be for the display function
                $rid = $matches[1];
                $args['rid'] = (int)$rid;
                $args['phase']='version';
                $args['exttype'] =(int)array_search($params[2], $flipext);
            }
        } elseif (!empty($params[2]) && preg_match('/^eid/i', $params[2]) && preg_match('/^(\d+)/', $params[3], $matches)) {
            $eid = $matches[1];
            $args['eid'] = (int)$eid;
            $args['phase']='version';
        } elseif (!empty($params[2]) && preg_match('/^(\d+)/', $params[2], $matches)) {
            $args['rid']=(int)$matches[1];
        }
        return ['display', $args];
    } elseif (preg_match('/^(\d+)/', $params[1], $matches)) {
        // something that starts with a number must try the display function for modules
        $rid = $matches[1];
        $args['rid'] = $rid;
        $args['exttype']=1;//module
        $args['phase']='view';
        return ['display', $args];
    } else {
        $cid = xarModVars::get('release', 'mastercids');
        if (xarMod::apiLoad('categories', 'user')) {
            $cats = xarMod::apiFunc(
                'categories',
                'user',
                'getcat',
                ['cid' => $cid,
                               'return_itself' => true,
                               'getchildren' => true, ]
            );
            // lower-case for fanciful search engines/people
            $params[1] = strtolower($params[1]);
            $foundcid = 0;
            foreach ($cats as $cat) {
                if ($params[1] == strtolower($cat['name'])) {
                    $foundcid = $cat['cid'];
                    break;
                }
            }
            // check if we found a matching category
            if (!empty($foundcid)) {
                $args['catid'] = $foundcid;
                // TODO: now analyse $params[2] for index, list, \d+ etc.
         // and return array('whatever', $args);
            }
        }
        // we have no idea what this virtual path could be, so we'll just
        // forget about trying to decode this thing
        // you *could* return the main function here if you want to
        return ['main', $args];
    }
    // default : return nothing -> no short URL decoded
}
