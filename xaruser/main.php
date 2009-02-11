<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * the main user function
 */
function publications_user_main($args)
{
    return xarModFunc('publications','user','view',$args);
// TODO: make this configurable someday ?
    // redirect to default view (with news publications)
    //xarResponseRedirect(xarModURL('publications', 'user', 'view'));
    //return;
}

?>
