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
         $statusmsg = xarSession::getVar( 'messages_statusmsg' );
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
