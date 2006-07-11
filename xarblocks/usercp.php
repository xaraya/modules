<?php
/**
 * User control panel block for Xar Authinvision
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
function authinvision_usercpblock_init() 
{
   return true;
}
function authinvision_usercpblock_info() 
{
    return array('text_type' => 'usercp',
                 'module' => 'authinvision',
                 'text_type_long' => 'Information and links for logged-in users');
}
function authinvision_usercpblock_display($blockinfo) 
{
     $uname = xarUserGetVar('uname');
     $newmessages = xarModAPIFunc('authinvision','user','getmessages',array('username'=>$uname));
     $boardlocation = xarModGetVar('authinvision','forumroot');
     $data['uname'] = $uname;
     $data['newmessages'] = $newmessages;
     $data['forumroot'] = $boardlocation;
     
     if (empty($blockinfo['template'])) {
        $template = 'usercp';
    } else {
        $template = $blockinfo['template'];
    }
     $blockinfo['content'] = xarTplBlock('authinvision', $template, $data);
     return $blockinfo;
}
function authinvision_usercpblock_modify() 
{
   return true;
}
function authinvision_usercpblock_update() 
{
   return true;
}
?>