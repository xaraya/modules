<?php
/**
 * File: $Id: handleexception.php,v 1.7 2003/07/18 19:41:17 garrett Exp $
 *
 * AddressBook utilapi handleException
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 */

/**
 * Intercepts and handles most Xaraya exceptions. Provides a cleaner handling
 * than the default system handling. After the exception has been handled it
 * is cleared, and processing continues as defined by the application
 *
 * @param array - this should be the same array that contains the template
 *                data. This func will add the additional params to the output
 *		          stream. The templates may then decide how best to display the
 *                error / warning / message. For SYSTEM_EXCEPTIONS we redirect
 *                the user to a special landing page and send a message to both
 *                the site admin and / or the developer (configured in Admin Panel)
 * @return multiple arrays, each is added to the output stream for display in the template
 * @return array xarSysException - if a SYSTEM_EXCEPTION is encountered
 * @return array abInfoMsg - if an _AB_ERR_INFO is encountered
 * @return array abWarnMsg - if an _AB_ERR_WARN is encountered
 * @return array abErroMsg - if an _AB_ERR_ERROR is encountered
 * @return array abDebugMsg - if an _AB_ERR_DEBUG is encountered 
 */
function AddressBook_utilapi_handleException ($args) {

    extract($args);

    /**
     * Check for any type of exception
     */
    if (xarExceptionMajor() != XAR_NO_EXCEPTION) {

        /**
         * Handle the exception
         */
        $abExceptions = array();

        foreach($GLOBALS['xarException_stack'] as $exception) {

            /**
             * This does not handle ErrorCollection Classes..
             */

            $abExc = new abUserException ($exception['value']);

            switch ($exception['major']) {
                case XAR_SYSTEM_EXCEPTION:
                    $xarException['type'] = $exception['exceptionId'];
                    $xarException['text'] = $abExc->abExceptionRender('html');
                    $abExceptions['xarSysException'][] = $xarException;
                    xarExceptionHandled();

				    /**
				     * if configured, kick out an email to the admin & developer
		             */		
		            $rptErrAdminFlag = xarModGetVar(__ADDRESSBOOK__,'rptErrAdminFlag');
				    $rptErrDevFlag = xarModGetVar(__ADDRESSBOOK__,'rptErrDevFlag');
		            if ($rptErrAdminFlag || $rptErrDevFlag) {
								
		  			    $to = array();
					    if ($rptErrAdminFlag) {
						    $adminEmail= xarModGetVar(__ADDRESSBOOK__,'rptErrAdminEmail');
							if (!xarModAPIFunc(__ADDRESSBOOK__,'util','is_email',array('email'=>$adminEmail))) {
								$adminEmail = "unknown@".$_SERVER['SERVER_NAME'];
							}
		
							$to[] = array ('name'=>"Site Admin"
							              ,'email'=>$adminEmail);
						}
						if ($rptErrDevFlag) {
							$to[] = array ('name'=>_AB_DEVQA_NAME
							               ,'email'=>_AB_DEVQA_EMAIL);
						}
			
					    $abModInfo = xarModGetInfo(xarModGetIDFromName(__ADDRESSBOOK__));
						$sendTo = '';
						$i=0;
						foreach ($to as $addr) {
							if ($i++ == 1) $sendTo .=",";
							$sendTo .= $addr['name']." <".$addr['email'].">";
						}						
	                	$from = __ADDRESSBOOK__." Module <".__ADDRESSBOOK__."@".$_SERVER['SERVER_NAME'].">";
	                	
	                	$subject = __ADDRESSBOOK__." Exception Raised";

						$message  = '';
						$message .= "You are receiving this email because your AddressBook ";
						$message .= "module is set to email error messages to your Site Admin account. ";
						$message .= "You can stop this behaviour by editing the Admin Messages ";
						$message .= "settings under Admin->AddressBook->ModifyConfig->AdminMessages.<br /><br />";
	                	$message .= "On ".strftime("%A, %D, at %H:%M", time())."<br />";
	                	$message .= "AddressBook ".$abModInfo['version']." Build "._AB_BUILD_VER."<br /><br />";
	                	$message .= "The following error(s) occurred.<br /><br />";
	                    $message .= $xarException['text'];
	                    $message .=

						$headers  = '';	
	                    $headers .= "From: ".$from."\n";
	                    $headers .= "Sender: ".$from."\n";
	                    $headers .= "MIME-Version: 1.0\n";
	                    $headers .= "Content-type: text/html; charset=iso-8859-1\n";
	
//	                    mail ($sendTo,$subject,$message,$headers,"-f".$from);
	                    mail ($sendTo,$subject,$message,$headers);
	                }	

                    break;

                case XAR_USER_EXCEPTION:
                    switch ($exception['exceptionId']) {
                        case _AB_ERR_INFO:
                            $abInfoMsg['type'] = $exception['exceptionId'];
                            $abInfoMsg['text'] = $abExc->abExceptionRender(_AB_ERR_INFO_STYLE);
                            $abExceptions['abInfoMsg'][] = $abInfoMsg;
                            break;

                        case _AB_ERR_WARN:
                            $abWarnMsg['type'] = $exception['exceptionId'];
                            $abWarnMsg['text'] = $abExc->abExceptionRender(_AB_ERR_WARN_STYLE);
                            $abExceptions['abWarnMsg'][] = $abWarnMsg;
                            break;

                        case _AB_ERR_ERROR:
                            $abErrMsg['type'] = $exception['exceptionId'];
                            $abErrMsg['text'] = $abExc->abExceptionRender(_AB_ERR_ERROR_STYLE);
                            $abExceptions['abErrMsg'][] = $abErrMsg;
                            break;

                        case _AB_ERR_DEBUG:
                            $abDebugMsg['type'] = $exception['exceptionId'];
                            $abDebugMsg['text'] = $abExc->abExceptionRender(_AB_ERR_DEBUG_STYLE);
                            $abExceptions['abDebugMsg'][] = $abDebugMsg;
                            break;
                    } // END switch
                    break;

                default:
    //              continue 2; //gehDEBUG not sure about this...
                    break;
            } // END switch
        }
        xarExceptionFree();

		$output['abExceptions'] = $abExceptions;
		/**
		 * For system errors we will redirect to a special error handling page
		 * for a more user friendly message
		 */
        if (isset($abExceptions['xarSysException'])) {
			$output = xarTplModule(__ADDRESSBOOK__,'user','error',$output);
        }
    } // END if

    return $output;

} // END handleException


?>
