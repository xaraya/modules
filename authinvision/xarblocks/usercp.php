<?php
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