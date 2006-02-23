<?php
/**
 * Block init - holds security.
 */
function pmember_subscriptionblock_init()
{
    // Security
    return true;
}
/**
 * block information array
 */
function pmember_subscriptionblock_info()
{
    return array('text_type' => 'Subscription',
         'text_type_long' => 'Display subscription icon in block format',
         'module' => 'pmember',
         'allow_multiple' => true,
         'form_content' => false,
         'form_refresh' => false,
         'show_preview' => true);
}
/**
 * block Display array
 */
function pmember_subscriptionblock_display($blockinfo)
{
    // Security Check
    if(!xarSecurityCheck('ViewPMember', 0)) return;

    if (empty($blockinfo['title'])){
        $blockinfor['title'] = xarML('Subscribe');
    }

    if (xarUserIsLoggedIn()){
        if (empty($blockinfo['template'])) {
            $template = 'subscription';
        } else {
            $template = $blockinfo['template'];
        }
        $blockinfo['content'] = xarTplBlock('pmember',$template);
    }
    return $blockinfo;
}
?>