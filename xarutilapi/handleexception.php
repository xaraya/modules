<?php
/**
 * AddressBook utilapi handleException
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
 *                stream. The templates may then decide how best to display the
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
function addressbook_utilapi_handleexception ($args)
{

    extract($args);

    /**
     * Check for any type of exception
     */
    if ((xarCurrentErrorType() != XAR_NO_EXCEPTION) || (xarIsCoreException())) {

        /**
         * Handle the exception
         */
        $abExceptions = array();

        global $ErrorStack, $CoreStack;
        while (!$ErrorStack->isempty() || !$CoreStack->isempty()) {

            if (!$CoreStack->isempty()) {
                $errorException = $CoreStack->pop();
            } else {
                if (!$ErrorStack->isempty()) {
                    $errorException = $ErrorStack->pop();
                } else {
                    $errorException = NULL;
                }
            }

            switch ($errorException->getMajor()) {
                case XAR_SYSTEM_EXCEPTION:
                    include_once ("includes/exceptions/htmlexceptionrendering.class.php");
                    include_once ("includes/exceptions/textexceptionrendering.class.php");

                    $xarException['type'] = $errorException->getID();

                    $htmlRendering = new HTMLExceptionRendering($errorException);
                    $textRendering = new TextExceptionRendering($errorException);
                    $xarException['text'] = $textRendering->getShort();
                    $xarException['htmltext'] = $htmlRendering->getShort();

                    $abExceptions['xarSysException'][] = $xarException;
                    xarErrorHandled();

                    /**
                     * if configured, kick out an email to the admin & developer
                     */
                    $rptErrAdminFlag = xarModGetVar('addressbook','rptErrAdminFlag');
                    $rptErrDevFlag = xarModGetVar('addressbook','rptErrDevFlag');
                    if ($rptErrAdminFlag || $rptErrDevFlag) {

                        $to = array();
                        if ($rptErrAdminFlag) {
                            $adminEmail= xarModGetVar('addressbook','rptErrAdminEmail');
                            if (!xarModAPIFunc('addressbook','util','is_email',array('email'=>$adminEmail))) {
                                $adminEmail = "unknown@".$_SERVER['SERVER_NAME'];
                            }

                            $to[] = array ('name'=>"Site Admin"
                                          ,'email'=>$adminEmail);
                        }
                        if ($rptErrDevFlag) {
                            $to[] = array ('name'=>_AB_DEVQA_NAME
                                           ,'email'=>_AB_DEVQA_EMAIL);
                        }

                        $abModInfo = xarModGetInfo(xarModGetIDFromName('addressbook'));

                        $from = array ('name' => 'addressbook'." Module"
                                      ,'email' => 'addressbook'."@".$_SERVER['SERVER_NAME']
                                      );

                        $subject = 'addressbook'." Exception Raised";

                        //Unformatted message used for both Plain Text & HTML emails
                        $message  = '';
                        $message .= "This is an automatically generated email from the Xaraya AddressBook Module. An error was encountered and this message will notify the development team to investigate.\n\n";

                        $message .= "If you do not wish to receive these emails, change the setting under Admin->AddressBook->ModifyConfig->AdminMessages. Visit %s for AddressBook updates.\n\n";

                        $message .= "**** Exception Details ****\n";
                        $message .= "Version: ".$abModInfo['version']."\n";
                        $message .= "The following error(s) occurred.\n\n";
                        $message .= $xarException['text'];

                        //Plain Text formatted message
                        $textmessage = sprintf ($message,"http://xaraya.blacktower.com");

                        //HTML formatted message
                        $htmlmessage = nl2br($message);
                        $htmlmessage = sprintf ($htmlmessage,"<a href=\"http://xaraya.blacktower.com\">http://xaraya.blacktower.com</a>");

                        // following vars are required by xarMail api
                        $xarMail = array (
                                 'subject' => $subject
                                ,'message' => $textmessage
                                ,'htmlmessage' => $htmlmessage
                                ,'from' => $from['email']
                                ,'fromname' => $from['name']
                                );
                        foreach ($to as $addr) {
                            $xarMail['info'] = $addr['email'];
                            $xarMail['name'] = $addr['name'];
                            xarModAPIFunc('mail','admin','sendmail',$xarMail);
                        }
                    }

                    break;

                case XAR_USER_EXCEPTION:
                    switch ($errorException->getID()) {
                        case _AB_ERR_INFO:
                            $abInfoMsg['type'] = $errorException->getID();
                            $abInfoMsg['text'] = $errorException->abExceptionRender(_AB_ERR_INFO_STYLE);
                            $abExceptions['abInfoMsg'][] = $abInfoMsg;
                            break;

                        case _AB_ERR_WARN:
                            $abWarnMsg['type'] = $errorException->getID();
                            $abWarnMsg['text'] = $errorException->abExceptionRender(_AB_ERR_WARN_STYLE);
                            $abExceptions['abWarnMsg'][] = $abWarnMsg;
                            break;

                        case _AB_ERR_ERROR:
                            $abErrMsg['type'] = $errorException->getID();
                            $abErrMsg['text'] = $errorException->abExceptionRender(_AB_ERR_ERROR_STYLE);
                            $abExceptions['abErrMsg'][] = $abErrMsg;
                            break;

                        case _AB_ERR_DEBUG:
                            $abDebugMsg['type'] = $errorException->getID();
                            $abDebugMsg['text'] = $errorException->abExceptionRender(_AB_ERR_DEBUG_STYLE);
                            $abExceptions['abDebugMsg'][] = $abDebugMsg;
                            break;
                    } // END switch
                    break;

                default:
    //              continue 2; //gehDEBUG not sure about this...
                    break;
            } // END switch
        } // END while
        xarErrorFree();
        xarCoreExceptionFree();

        $output['abExceptions'] = $abExceptions;

        /**
         * For system errors we will redirect to a special error handling page
         * for a more user friendly message
         */
        if (isset($abExceptions['xarSysException'])) {
            $output = xarTplModule('addressbook','user','error',$output);
        }
    } // END if

    return $output;

} // END handleException

?>
