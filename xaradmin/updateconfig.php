<?php
/**
 * Xaraya Smilies Update Module Config
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
 * @returns output
 * @return output with smilies configuration settings
 */
function smilies_admin_updateconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminSmilies')) return;

    if (!xarVarFetch('itemsperpage',    'int',      $itemsperpage,    20,         XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('skiptags',        'str:1',    $skiptags,        '',         XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showduplicates',  'checkbox', $showduplicates,  false,      XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('image_folder',    'str',      $image_folder,    '',  XARVAR_NOT_REQUIRED)) return; 
    if (!xarVarFetch('allowhookoverride', 'checkbox', $allowhookoverride, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarSecConfirmAuthKey()) return;

    // Bug 3957: allow admin to specify html tags to ignore when transforming
    $seentags = array();
    if (!empty($skiptags)) {
      // TODO: make this list complete
      $alltags = array('div','p','b','a','blockquote','code','table','tr','td','thead','th','tfoot','span','textarea','input','label','fieldset','form','legend');
      // strip any spaces from input
      $skiptags = str_replace(' ', '', $skiptags);
      $tagstoskip = explode(',', $skiptags);
      foreach ($tagstoskip as $htmltag) {
        // skip invalid tags
        if (!in_array($htmltag, $alltags)) continue;
        $seentags[$htmltag] = 1;
      }
    }
    if (!empty($seentags)) {
      $seentags = array_keys($seentags);
    }
    xarModSetVar('smilies', 'skiptags', serialize($seentags));
    
    // Bug 5116: option to hide duplicate emotions in user main
    xarModSetVar('smilies', 'showduplicates', $showduplicates);
    // Bug 5271: allow specifying a subfolder from which to serve smilies images
    if (!empty($image_folder)) {
      $themedir = xarTplGetThemeDir();
      // make sure we have a folder somewhere by this name
      if (!file_exists('modules/smilies/xarimages/'.$image_folder) && !file_exists($themedir.'/modules/smilies/images/'.$image_folder)) {
        // and if not, use the default folder
        $image_folder = '';
      }
    }
    xarModSetVar('smilies', 'image_folder', $image_folder);
    // make use of the unused itemsperpage mod var
    xarModSetVar('smilies', 'itemsperpage', $itemsperpage);
    xarModSetVar('smilies', 'allowhookoverride', $allowhookoverride);

    xarModCallHooks('module', 'updateconfig', 'smilies', array('module' => 'smilies'));
    // set a status message confirming settings updated
    xarSessionSetVar('statusmessage', xarML('Settings Updated'));
    // this function generated no output so we return to modifyconfig
    xarResponseRedirect(xarModURL('smilies', 'admin', 'modifyconfig'));

    return true;
    
}
?>