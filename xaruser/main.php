<?php
/**
 * The main user function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
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
 * @author the Example module development team
 * @return array $data An array with the data for the template
 */
function twitter_user_main()
{

    if (!xarSecurityCheck('ViewTwitter')) return;
    if (!xarVarFetch('timeline', 'str:1', $timeline, '', XARVAR_NOT_REQUIRED)) return;

    $data = array();

    $data['username'] = xarModGetVar('twitter', 'username');
    $password = xarModGetVar('twitter', 'password');
    $data['isowner']    = xarUserGetVar('uid') == xarModGetVar('twitter', 'owner') ? true : false;
    $data['itemsperpage'] = xarModGetVar('twitter', 'itemsperpage');
    $data['showpublic'] = xarModGetVar('twitter', 'showpublic');
    $data['showuser'] = xarModGetVar('twitter', 'showuser');
    $data['showfriends'] = xarModGetVar('twitter', 'showfriends');
    $data['deftimeline'] = xarModGetVar('twitter', 'deftimeline');

    $timelines = array();
    if ($data['showpublic']) $timelines[] = array('id' => 'public', 'name' => 'Public');
    if (!empty($data['username']) && !empty($password)) {
      if ($data['showuser']) $timelines[] = array('id' => 'user', 'name' => 'User');
      if ($data['showfriends']) $timelines[] = array('id' => 'friends', 'name' => 'Friends');
    }
    $data['timelines'] = $timelines;

    if (empty($timeline)) $timeline = $data['deftimeline'];
    $data['timeline'] = $timeline;
    
    $items = xarModAPIFunc('twitter', 'user', 'status_methods',
      array(
        'method' => $timeline.'_timeline',
        'username' => $data['username'],
        'password' => $password
      ));

    $data['items'] = $items;   
    $data['activetab'] = $timeline;
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Timeline')));
    /* Return the template variables defined in this function */
    return $data;

}
?>