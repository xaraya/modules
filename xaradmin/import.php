<?php
/**
 * Manually import emails
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage pop3gateway Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author John Cox
 */

/**
 * Manually import emails
 * 
 * @author John Cox
 */
function pop3gateway_admin_import()
{
    if(!xarSecurityCheck('AdminPOP3Gateway')) return;
    require_once('modules/pop3gateway/xarclass/class.pop3.php');
    $phone_delim    = '::';
    $mailserver     = xarModGetVar('pop3gateway', 'mailserver');
    $mailserverlogin= xarModGetVar('pop3gateway', 'mailserverlogin');
    $mailserverpass = xarModGetVar('pop3gateway', 'mailserverpass');
    $mailserverport = xarModGetVar('pop3gateway', 'mailserverport');
    $pop3           = new POP3();
    
    if (!$pop3->connect($mailserver, $mailserverport)){
        $msg = xarML('Invalid #(1)', $pop3->ERROR);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $count = $pop3->login($mailserverlogin, $mailserverpass);

    if ($count == 0){
        $msg = xarML('No Mail To Import from #(1)', $mailserver);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    for ($i=1; $i <= $count; $i++) {
        $message        = $pop3->get($i);
        $content        = '';
        $content_type   = '';
        $boundary       = '';
        $bodysignal     = 0;
        //$dmonths        = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        foreach ($message as $line){
            if (strlen($line) < 3) $bodysignal = 1;

            if ($bodysignal) {
                $content .= $line;
            } else {
                if (preg_match('/Content-Type: /i', $line)) {
                    $content_type = trim($line);
                    $content_type = substr($content_type, 14, strlen($content_type)-14);
                    $content_type = explode(';', $content_type);
                    $content_type = $content_type[0];
                }
                if (($content_type == 'multipart/alternative') && (preg_match('/boundary="/', $line)) && ($boundary == '')) {
                    $boundary = trim($line);
                    $boundary = explode('"', $boundary);
                    $boundary = $boundary[1];
                }
                if (preg_match('/Subject: /i', $line)) {
                    $subject = trim($line);
                    $subject = substr($subject, 9, strlen($subject)-9);
                    // Captures any text in the subject before $phone_delim as the subject
                    $subject = explode($phone_delim, $subject);
                    $subject = $subject[0];
                }
            }
        }

        //$subject = trim(str_replace(get_settings('subjectprefix'), '', $subject));

        if ($content_type == 'multipart/alternative') {
            $content = explode('--'.$boundary, $content);
            $content = $content[2];
            $content = explode('Content-Transfer-Encoding: quoted-printable', $content);
            $content = strip_tags($content[1], '<img><p><br><i><b><u><em><strong><strike><font><span><div>');
        }
        $content = trim($content);
        // Captures any text in the body after $phone_delim as the body
        $content = explode($phone_delim, $content);
        //$test = var_export($content); return "<pre>$test</pre>";
        if (isset($content[1])){
            $content = $content[1];
        } else {
            $content = $content[0];
        }
        $article = array();
        $content = str_replace("\r\n", " ", $content);
        if (preg_match("/\[trackback\](.*?)\[\/trackback\]/si", $content, $tb)){
            $article['tb_pingurl'] = $tb[1];
            $content = preg_replace("/\[trackback\](.*?)\[\/trackback\]/si", "", $content);
        }
        if (preg_match("/\[category\](.*?)\[\/category\]/si", $content, $cids)){
            $article['cids'] = array($cids[1]);
            $content = preg_replace("/\[category\](.*?)\[\/category\]/si", "", $content);
        }
        if (preg_match("/\[summary\](.*?)\[\/summary\]/si", $content, $summary)){
            $article['summary'] = $summary[1];
            $content = preg_replace("/\[summary\](.*?)\[\/summary\]/si", "", $content);
        } else {
            $article['summary'] = $content;
        }
        if (preg_match("/\[body\](.*?)\[\/body\]/si", $content, $body)){
            $article['body'] = $body[1];
            $content = preg_replace("/\[body\](.*?)\[\/body\]/si", "", $content);
        }
        if (preg_match("/\[notes\](.*?)\[\/notes\]/si", $content, $notes)){
            $article['notes'] = $notes[1];
            $content = preg_replace("/\[notes\](.*?)\[\/notes\]/si", "", $content);
        }
        $importpubtype = xarModGetVar('pop3gateway','importpubtype');
        if (empty($importpubtype)) {
            $importpubtype = xarModGetVar('articles','defaultpubtype');
            if (empty($importpubtype)) {
                $importpubtype = 1;
            }
            xarModSetVar('pop3gateway','importpubtype',1);
        }
        $defaultstatus = xarModGetVar('pop3gateway', 'defaultstatus');
        if (empty($defaultstatus)){
            $defaultstatus = 0;
        }
        $article['title']   = $subject;
        $article['aid'] = 0;
        $article['ptid'] = $importpubtype;
        $article['status'] = (int)$defaultstatus;
        xarModAPIFunc('articles', 'admin', 'create', $article);
        // Delete mail
        if(xarModGetVar('pop3gateway', 'DeleteMailAfter')) {
            if(!$pop3->delete($i)) {
                $msg = xarML('Invalid #(1)', $pop3->ERROR);
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                $pop3->reset();
                return;
            }
        }
    }
    $pop3->quit();
    xarResponseRedirect(xarModURL('articles', 'admin', 'view'));
}
?>