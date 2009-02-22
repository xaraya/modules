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
 * The main user function
 *
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments. As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array $data An array with the data for the template
 */
function twitter_user_main()
{

    if (!xarSecurityCheck('ViewTwitter')) return;
    if (!xarVarFetch('timeline', 'str:1', $timeline, '', XARVAR_NOT_REQUIRED)) return;
    
    /* somebody clicked user-main from the menu */
    if (empty($timeline)) {
      $timeline = xarModGetVar('twitter', 'main_tab');
    }
    // this gets everything we need
    $data = xarModAPIFunc('twitter', 'user', 'menu', 
      array('modtype' => 'user', 'modfunc' => 'main', 'timeline' => $timeline));
    
    // see if this tag is supposed to be shown
    $showtab = $timeline == 'new_tweet' || xarModGetVar('twitter', $timeline) ? true : false;
    
    $items = array();
    // we're showing this tab
    if ($showtab) {
      if ($timeline == 'account_display' && !empty($data['site_account'])) {
        if ($data['isowner']) {
          return xarResponseRedirect(xarModURL('twitter', 'user', 'account', array('screen_name' => $data['site_account']['screen_name'])));
        } else {
          return xarResponseRedirect(xarModURL('twitter', 'user', 'display', array('screen_name' => $data['site_account']['screen_name'])));
        }
      } elseif ($timeline == 'users_display' && !empty($data['t_fieldname'])) {
        return xarResponseRedirect(xarModURL('twitter', 'user', 'view'));
      } elseif ($timeline == 'new_tweet') {
        return xarResponseRedirect(xarModURL('twitter', 'user', 'tweet'));
      } else {
        $items = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
            'area' => 'statuses',
            'method' => 'public_timeline',
            'cached' => true,
            'refresh' => 60,
            'superrors' => true
          ));
      }
    }

    $data['status_elements'] = $items;
    $data['activetab'] = $timeline;
    $data['timeline'] = $timeline;
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('#(1) timeline',ucfirst($timeline))));
    /* Return the template variables defined in this function */
    return $data;

}
?>