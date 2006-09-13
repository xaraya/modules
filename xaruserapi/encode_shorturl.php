<?php
/**
 * Support for short URLs (user functions)
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */

/**
 * return the path for a short URL to xarModURL for this module
 * 
 * @author jojodee
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function release_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }
    //get the extension types
    $exttypes = xarModAPIFunc('release','user','getexttypes');
    $extname ='';
    if (isset($exttype)) {
        $exttypename = array_search($exttype,$exttypes);
        $extname = strtolower($exttypename);
    }

    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'release';

    if ($func == 'main') {
        $path = '/' . $module . '/';
    } elseif ($func == 'view') {
        if (isset($sort)) {
            $path = '/' . $module . '/view/' . $sort . '.html';
        } else {
             $path = '/' . $module . '/view.html';
        }
    } elseif ($func == 'display') {
        // check for required parameters
        if (isset($rid) && is_numeric($rid)) {
           if (!isset($exttype)) {// have to assume it's a module for backward compatibility ==1
               if (isset($phase) && $phase=='version'){
                    $path = '/' . $module . '/version/module/' . $rid . '.html';
               }elseif (isset($phase) && $phase=='view'){
                    $path = '/' . $module . '/module/' . $rid . '.html';
               }else {
                    $path = '/' . $module . '/module/' . $rid . '.html';
               }
           }else {
                if (isset($phase) && $phase=='version'){
                    $path = '/' . $module . '/version/'.$extname.'/' . $rid . '.html';
               }elseif (isset($phase) && $phase=='view'){
                    $path = '/' . $module . '/' . $extname.'/' .$rid . '.html';
               }else {
                    $path = '/' . $module . '/' .$extname.'/' . $rid . '.html';
                }
           }

        } elseif (isset($eid)) {
            if (isset($phase) && $phase=='version'){
                    $path = '/' . $module . '/version/eid/' . $eid;
            }elseif (isset($phase) && $phase=='view'){
                    $path = '/' . $module . '/eid/' . $eid;
            }else {
                    $path = '/' . $module . '/eid/' . $eid;
            }
        }
    } elseif ($func == 'viewnotes') {
        $path = '/' . $module . '/viewnotes.html';

    } elseif ($func == 'displaynote') {
        // check for required parameters
        if (isset($rnid) && is_numeric($rnid)) {
            $path = '/' . $module . '/displaynote/' . $rnid . '.html';
        } else {
        }

    } elseif ($func == 'addnotes') {
        // check for required parameters
        if (isset($rid)){
            if (!isset($exttype)){ //have to assume a module .. backward compatibility, ugg
                if (isset($phase) && ($phase=='start')) {
                    $path = '/' . $module . '/addnotes/start/module/' . $rid . '.html';
                } else {
                     $path = '/' . $module . '/module/addnotes.html';
                }
            }else {
                if (isset($phase) && ($phase=='start')) {
                    $path = '/' . $module . '/addnotes/start/'.$extname.'/' . $rid . '.html';
                } else {
                     $path = '/' . $module . '/'.$extname.'/addnotes.html';
                }
            }
        }elseif (isset($eid)){
           if (isset($phase) && ($phase=='start')) {
                    $path = '/' . $module . '/addnotes/start/eid/' . $eid;
           } else {
                     $path = '/' . $module . '/eid/addnotes.html';
           }
        }
    } elseif ($func == 'addid') {
        $path = '/' . $module . '/addid.html';

    } elseif ($func == 'modifyid') {
        if (isset($rid)) {
            if (!isset($exttype)){ //have to assume a module .. backward compatibility, ugg
                // check for required parameters
                if (isset($rid) && is_numeric($rid)) {
                    $path = '/' . $module . '/modifyid/module/' . $rid . '.html';
                } else {
                }
            }else {
                if (isset($rid) && is_numeric($rid)) {
                    $path = '/' . $module . '/modifyid/'.$extname.'/'. $rid . '.html';
               }
            }
        }elseif (isset($eid)){
             $path = '/' . $module . '/modifyid/eid/'. $eid;
        }

    } else {
        // -> don't create a path here
    }
    // add some other module arguments as standard URL parameters
    if (!empty($path)) {
        if (isset($startnum)) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&';
        } 
        if (!empty($catid)) {
            $path .= $join . 'catid=' . $catid;
            $join = '&';
        } elseif (!empty($cids) && count($cids) > 0) {
            if (!empty($andcids)) {
                $catid = join('+', $cids);
            } else {
                $catid = join('-', $cids);
            } 
            $path .= $join . 'catid=' . $catid;
            $join = '&';
        } 
    } 

    return $path;
} 

?>