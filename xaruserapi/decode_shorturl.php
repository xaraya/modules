<?php
/**
 * Extract function and arguments from short URLs for this module
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
    $args = array(); 
    if ($params[0] == 'rid') {
        if (is_numeric($params[1])) { 
            $args['rid'] = (int) $params[1];
            return array('display', $args);
        } else {
            //Lots of hits for categories... But how can we discover them
            //as what we know about them is c107 or c31? Time for Good Urls instead of Short?
            return array('view', $args);
        }
    } elseif (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('main', $args);
    } elseif (preg_match('/^index/i', $params[1])) {
        // some search engine/someone tried using index.html (or similar)
        // -> we'll go to the main function
        return array('main', $args);
    } elseif (preg_match('/^viewnotes/i', $params[1])) {
            return array('viewnotes', $args);
    } elseif (preg_match('/^view/i', $params[1])) {
        if (!empty($params[2]) && ($params[2] == 'id.html')) {
          $args['sort']='id';
        }elseif (!empty($params[2]) && ($params[2] == 'name.html')) {
          $args['sort']='name';
        }elseif (!empty($params[2]) && ($params[2] == 'author.html')) {
          $args['sort']='author';
        }
        return array('view', $args);
    } elseif (preg_match('/^addid/i', $params[1])) {
            return array('addid', $args);
    } elseif ($params[1] == 'displaynote') {
        if (!empty($params[2]) && preg_match('/^(\d+)/',$params[2],$matches)) {
            $args['rnid']=$matches[1];
            return array('displaynote', $args);
        }
    }elseif (preg_match('/^addnotes/i', $params[1])) {
        if (empty($params[2])) {
           return array('addnotes', $args);
        }elseif (!empty($params[2]) && ($params[2] == 'start')) {
           $args['phase']='start';
        }
        if (!empty($params[3]) && preg_match('/^(\d+)/',$params[3],$matches)) {
            $args['rid']=$matches[1];
        }
        return array('addnotes', $args);
   } elseif ($params[1] == 'modifyid') {
        if (!empty($params[2]) && preg_match('/^(\d+)/',$params[2],$matches)) {
            $args['rid']=$matches[1];
            return array('modifyid', $args);
        }
    } elseif ($params[1] == 'version') {
        if (!empty($params[2]) && preg_match('/^(\d+)/', $params[2], $matches)){
        // something that starts with a number must be for the display function
            $rid = $matches[1];
            $args['rid'] = (int)$rid;
            $args['phase']='version';
        }
        return array('display', $args);
    } elseif (preg_match('/^(\d+)/', $params[1], $matches)) {
        // something that starts with a number must be for the display function
        $rid = $matches[1];
        $args['rid'] = $rid;
        $args['phase']='view';
        return array('display', $args);
    } else {
        $cid = xarModGetVar('release','mastercids');
        if (xarModAPILoad('categories','user')) {
            $cats = xarModAPIFunc('categories',
                              'user',
                              'getcat',
                         array('cid' => $cid,
                               'return_itself' => true,
                               'getchildren' => true));
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
         return array('main', $args);
    }
    // default : return nothing -> no short URL decoded
}

?>
