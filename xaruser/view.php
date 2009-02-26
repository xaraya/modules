<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
/**
 * View a list of items
 *
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array $data array with all information for the template
 */
function twitter_user_view($args)
{
    if (!xarSecurityCheck('ReadTwitter')) return;

    $data = xarModAPIFunc('twitter', 'user', 'menu', array('modtype' => 'user', 'modfunc' => 'view'));

    if (!empty($data['t_fieldname'] )) {
      $data['items'] = xarModAPIFunc('dynamicdata', 'user', 'getitems', array('module' => 'roles', 'itemtype' => 0, 'where' => $data['t_fieldname']. ' ne ""', 'fieldlist' => $data['t_fieldname']));
    }

    return $data;
}
?>