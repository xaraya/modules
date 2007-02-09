<?php
/**
* Update the configuration
*
* @package unassigned
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Update the configuration
*
* @author  Curtis Farnham <curtis@farnham.com>
* @access  public
* @return  true on success or void on failure
* @throws  NO_PERMISSION
*/
function highlight_admin_modifyconfig()
{
    // security check
    if (!xarSecurityCheck('AdminHighlight')) return;

    // get vars
    $authid = xarSecGenAuthKey();
    $supportshorturls = xarModGetVar('highlight', 'SupportShortURLs');
    $string = xarModGetVar('highlight', 'string');
    $string = htmlspecialchars($string);

    // initialize template data
    $data = xarModAPIFunc('highlight', 'admin', 'menu');

    // generate template vars
    $data['string'] = $string;
    $data['authid'] = $authid;
    $data['supportshorturls'] = $supportshorturls;

    return $data;
}

?>
