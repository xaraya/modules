<?php
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