<?php
/**
 * Xaraya Smilies
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/
function smilies_admin_view()
{
    // Security Check
    if(!xarSecurityCheck('EditSmilies')) return;
    if(!xarVarFetch('startnum', 'isset',    $startnum, 1,     XARVAR_NOT_REQUIRED)) {return;}
    $data['items'] = array();
    // Specify some labels for display
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('smilies', 'user', 'countitems'),
                                    xarModURL('smilies', 'admin', 'view', array('startnum' => '%%')),
                                    xarModGetVar('smilies', 'itemsperpage'));
    // The user API function is called
    $links = xarModAPIFunc('smilies',
                           'user',
                           'getall',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('smilies',
                                                            'itemsperpage')));

    if (empty($links)) {
        $msg = xarML('There are no smilies registered');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    
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

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        if (xarSecurityCheck('EditSmilies',0)) {
            $links[$i]['editurl'] = xarModURL('smilies',
                                              'admin',
                                              'modify',
                                              array('sid' => $link['sid']));
        } else {
            $links[$i]['editurl'] = '';
        }
        $links[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteSmilies',0)) {
            $links[$i]['deleteurl'] = xarModURL('smilies',
                                               'admin',
                                               'delete',
                                               array('sid' => $link['sid']));
        } else {
            $links[$i]['deleteurl'] = '';
        }
        $links[$i]['deletetitle'] = xarML('Delete');
        // Bug 5271:
        if (!empty($image_folder)) {
          // look for the smiley in the subfolder of the module and theme images folders
          if (file_exists('modules/smilies/xarimages/'.$image_folder.'/'.$links[$i]['icon']) || file_exists($themedir.'/modules/smilies/images/'.$image_folder.'/'.$links[$i]['icon'])) {
            // if we found one, use it
            $links[$i]['icon'] = $image_folder . '/' . $links[$i]['icon'];
          }
        }
    }
    // Add the array of items to the template variables
    $data['items'] = $links;
    // Return the template variables defined in this function
    return $data;
}
?>