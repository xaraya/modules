<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_admin_main()
{

    if (!xarSecurityCheck( 'EditMessages')) return;

    // Check if we should show the overview page
    // The admin system looks for a var to be set to skip the introduction
    // page altogether.  This allows you to add sparse documentation about the
    // module, and allow the site admins to turn it on and off as they see fit.
    if (xarModGetVar('adminpanels', 'overview') == 0) {

        // Yes we should
        $data = messages_admin_common( 'Overview' );
        return $data;

    }

    // No we shouldn't. So we redirect to the admin_view() function.
    xarResponseRedirect(
        xarModURL(
            'messages'
            ,'admin'
            ,'config' ));
    return true;

}

function messages_admin_common( $title = 'Undefined' )
{
         $common = array();
         $common['menu'] = array();

         // Initialize the statusmessage
         $statusmsg = xarSessionGetVar( 'messages_statusmsg' );
         if ( isset($statusmsg)){
              xarSessionDelVar('messages_statusmsg');
         }

         // Set the page title
         xarTplSetPageTitle( 'messages :: ' . $title );

         // Initialize the title
         $common['pagetitle'] = $title;
         $common['type'] = 'Messages Administration';

         return array( 'common' => $common );
}

?>
