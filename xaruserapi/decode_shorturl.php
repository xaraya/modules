<?php
/**
 * Twitter Module 
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
/**
 * Extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @param  array $params array containing the different elements of the virtual path
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function twitter_userapi_decode_shorturl($params)
{
    /* Initialise the argument list we will return */
    $args = array();
    $module = 'twitter';
    /* Check and see if we have a module alias */
    $aliasisset = xarModGetVar($module, 'useModuleAlias');
    $aliasname = xarModGetVar($module,'aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }

    /* Analyse the different parts of the virtual path
     * $params[1] contains the first part after index.php/example
     * In general, you should be strict in encoding URLs, but as liberal
     * as possible in trying to decode them...
     */
    if ($params[0] != $module) { /* it's possibly some type of alias */
        $aliasname = xarModGetVar($module,'aliasname');
    }
    if (empty($params[1])) {
        /*( nothing specified -> we'll go to the main function */
        return array('main', $args);
    } elseif (preg_match('/^index/i', $params[1])) {
        return array('main', $args);
    } elseif (preg_match('/^tweet/i', $params[1])) {
        return array('tweet', $args);
    } elseif (preg_match('/^list/i', $params[1])) {
        return array('view', $args);
    } elseif (preg_match('/^public/i', $params[1])) {
      $args['timeline'] = 'public_timeline';
      return array('main', $args);
    } elseif (preg_match('/^(\w+)/', $params[1], $matches)) {
      $screen_name = $params[1];
      $args['screen_name'] = $screen_name;
      $site_screen_name = xarModGetVar('twitter', 'site_screen_name');
      $urlparam = 'display';
      if ($screen_name == $site_screen_name && xarUserGetVar('uid') == xarModGetVar('twitter', 'site_screen_role')) {
        $urlparam = 'account';
      } else {
        $t_fieldname = xarModGetVar('twitter', 'fieldname');
        if (!empty($t_fieldname)) {
          $userdd = !empty($t_fieldname) ? xarUserGetVar($t_fieldname) : '';
          if (!empty($userdd) && strpos($userdd, ',') !== false) {
            list ($user_screen_name) = explode(',', $userdd);
          }
          if (!empty($user_screen_name)) {
            $urlparam = 'account';
          }
        }
      }
      if (!empty($params[2]) && (preg_match('/^(\w+)/', $params[2], $matches))) {
        $args['tab'] = $matches[1];
      }
      return array($urlparam, $args);
    } else {
      return array('main', $args);
    }
    /* default : return nothing -> no short URL decoded */
}
?>