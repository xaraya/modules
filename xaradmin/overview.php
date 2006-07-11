<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authemail Module
 * @link http://xaraya.com/index.php/release/10513.html
*/
/**
 * Overview function that displays the standard Overview page
 *
 * @author jojodee
 * @return array xarTplModule with $data containing template data
 */
function authemail_admin_overview()
{

    /* provide some information for users and ensure the module is in the module listing 
     *  as a cue for admins so they can see what is installed
     */
     $data=array();
    return xarTplModule('authemail', 'admin', 'main', $data,'main');
}

?>