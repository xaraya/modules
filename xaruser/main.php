<?php
/* * Xaraya Smilies
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with smilies Menu information
 */
function smilies_user_main()
{
    // Security Check
    if(!xarSecurityCheck('OverviewSmilies')) return;
    // Get parameters from whatever input we need
    if(!xarVarFetch('startnum', 'isset',    $startnum, 1,     XARVAR_NOT_REQUIRED)) {return;}

    // check to see if the print theme was called for documentation
    $theme = xarVarGetCached('Themes.name','CurrentTheme');
    if ($theme == 'print'){
        $print = true;
    }

    $data['items'] = array();
 
    // Bug 5116: option to hide duplicate emotions in user main
    $showduplicates = xarModGetVar('smilies', 'showduplicates');
    
    $groupby = $showduplicates ? NULL : 'emotion';
    
    // Specify some labels for display
    if (isset($print)){
        $data['print'] = true;
        // if the theme is print, allow over-riding itemsperpage from input, taking the default if none found
        if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, xarModGetUserVar('smilies', 'itemsperpage'), XARVAR_NOT_REQUIRED)) return;
        $data['pager'] = xarTplGetPager($startnum,
                                        xarModAPIFunc('smilies', 'user', 'countitems', array('groupby' => $groupby)),
                                        xarModURL('smilies', 'user', 'main', array('startnum' => '%%', 'theme' => 'print')),
                                        $itemsperpage);
    } else {
        $data['print'] = false;
        $itemsperpage = xarModGetUserVar('smilies', 'itemsperpage');
        $data['pager'] = xarTplGetPager($startnum,
                                        xarModAPIFunc('smilies', 'user', 'countitems', array('groupby' => $groupby)),
                                        xarModURL('smilies', 'user', 'main', array('startnum' => '%%')),
                                        $itemsperpage);
    }

    // The user API function is called
    $links = xarModAPIFunc('smilies',
                           'user',
                           'getall',
                           array('startnum' => $startnum,
                                 'numitems' => $itemsperpage,
                                 'groupby'  => $groupby // added for Bug 5116
                            ));

    // CHECKME: instead of throwing an exception here, 
    // could we not just let the template handle no smilies with a graceful message?
    if (empty($links)) {
        $msg = xarML('There are no smilies registered');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    
    // Bug 5271: allow different folder to be specified to over-ride smilie images
    $image_folder = xarModGetVar('smilies', 'image_folder');
    if (!empty($image_folder)) {
      $themedir = xarTplGetThemeDir();
      $items = array();
      foreach ($links as $key => $link) {
         $item = $links[$key];
        // look for the smiley in the subfolder of the module and theme images folders
        if (file_exists('modules/smilies/xarimages/'.$image_folder.'/'.$links[$key]['icon']) || file_exists($themedir.'/modules/smilies/images/'.$image_folder.'/'.$links[$key]['icon'])) {
          // if we found one, use it
          $item['icon'] = $image_folder . '/' . $links[$key]['icon'];
        }
        $items[$key] = $item;
      }
      $links = $items;
    }

    // Add the array of items to the template variables
    $data['items'] = $links;
    // Return the template variables defined in this function
    return $data;
}
?>