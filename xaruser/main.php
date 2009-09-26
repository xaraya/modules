<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * the main user function
 */
function articles_user_main($args)
{
    return xarMod::guiFunc('articles','user','view',$args);
// TODO: make this configurable someday ?
    // redirect to default view (with news articles)
    //xarResponse::Redirect(xarModURL('articles', 'user', 'view'));
    //return;
}

?>
