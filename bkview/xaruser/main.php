<?php

/**
 * File: $Id$
 *
 * Mainn function for bkview 
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @link  link to where more info can be found
 * @author author name <author@email> (this tag means responsible person)
*/


/**
 * the main user function
 */
function bkview_user_main()
{
    $data = xarModFunc('bkview','user','view');
    return $data;
}
?>
