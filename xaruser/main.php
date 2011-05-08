<?php
/**
 * Publications module
 *
 * @package modules
 * @subpackage Publications Module 
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf (mfl@netspan.ch)
 *
 */
/**
 * the main user function
 */
function publications_user_main($args)
{
    return xarModFunc('publications','user','view',$args);
}

?>