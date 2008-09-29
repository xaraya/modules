<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Smilies
 * @link http://xaraya.com/index.php/release/153.html
 */
/**
 * Overview function that displays the standard Overview page
 *
 */
function smilies_admin_overview()
{
    // Security Check
    if (!xarSecurityCheck('AdminSmilies',0)) return;

    $data = array();

    // Get the current smilies for an overview.
    $smilies = xarModAPIFunc('smilies', 'user', 'getall');
    
    // Bug 5271:
    $image_folder = xarModGetVar('smilies', 'image_folder');
    if (!empty($image_folder)) {
      $themedir = xarTplGetThemeDir();
      // make sure we have a folder somewhere by this name
      if (!file_exists('modules/smilies/xarimages/'.$image_folder) && !file_exists($themedir.'/modules/smilies/images/'.$image_folder)) {
        // and if not, use the default folder
        $image_folder = '';
      }
    }

    // Sort by icon
    foreach($smilies as $smilie) {
        // Bug 5271:
        if (!empty($image_folder)) {
          // look for the smiley in the subfolder of the module and theme images folders
          if (file_exists('modules/smilies/xarimages/'.$image_folder.'/'.$smilie['icon']) || file_exists($themedir.'/modules/smilies/images/'.$image_folder.'/'.$smilie['icon'])) {
            // if we found one, use it
            $smilie['icon']= $image_folder . '/' . $smilie['icon'];
          }
        }
        $data['icons'][$smilie['icon']][] = $smilie;
    }

    // if there is a separate overview function return data to it
    // else just call the main function that displays the overview

    return xarTplModule('smilies', 'admin', 'main', $data, 'main');
}

?>
