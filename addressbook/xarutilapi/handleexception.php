<?php
/**
 * File: $Id: handleexception.php,v 1.2 2003/07/05 04:11:21 garrett Exp $
 *
 * AddressBook utility functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
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
                     * Only send mail when we are NOT debugging
                     */
                    if (!_AB_DEBUG) {
                        $message = $xarException['text'];

                        $headers  = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                        $headers .= "From: XARAYA_AT_BLACKTOWER <xaraya@".$_SERVER['SERVER_NAME'].">";

                        mail ("AB BUG REPORT <xaraya@blacktower.com>","Xaraya System Exception",$message,$headers,"-fxaraya@".$_SERVER['SERVER_NAME']);
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