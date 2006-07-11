<?php
/**
 * Register block
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authinvision
 * @link http://xaraya.com/index.php/release/950.html
 * @author ladyofdragons
 */
 
function authinvision_registerblock_init() 
{
   return true;
}
function authinvision_registerblock_info() 
{
    return array('text_type' => 'invisionregister',
                 'module' => 'authinvision',
                 'text_type_long' => 'Register with Invision Board');
}
function authinvision_registerblock_display($blockinfo) 
{
     $mainfile = xarModGetVar('authinivision','mainfile');
     require($mainfile);
     $regtext = show_reg();
}
function authinvision_registerblock_modify() 
{
   return true;
}
function authinvision_registerblock_update() 
{
   return true;
}
?>