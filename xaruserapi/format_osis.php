<?php
/**
 * File: $Id:
 * 
 * Format a string for OSIS format
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
/**
 * format OSIS string
 * 
 * @author curtisdf 
 * @param  $args ['html'] results array
 * @param  $args ['strongs'] boolean, whether or not to format with strong's numbers
 * @returns string
 * @return formatted string, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_format_osis($args)
{
    extract($args);

    // default is to display strong's numbers
    if (!isset($strongs)) $strongs = true;

    if (!isset($html) || !is_string($html)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'html', 'user', 'format_osis', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // w (Strong's tags)
    if ($strongs) {
        preg_match_all("/<w [^>]*savlm=\"x\-Strongs\:([HG])(\d+)\" ?[^>]*>([^>]*)<\/w>/i", $html, $matches);
        foreach ($matches[0] as $index => $match) {
            $matches[2][$index] = preg_replace("/^0*/", '', $matches[2][$index]);
            if ($matches[1][$index] == 'G') {
                $sname = 'StrongsGreek';
            } elseif ($matches[1][$index] == 'H') {
                $sname = 'StrongsHebrew';
            }
            $url = xarModURL('bible', 'user', 'strongs', array('sname' => $sname, 'query' => $matches[2][$index], 'string' => $matches[3][$index]));
            $html = str_replace($match, "<a href=\"$url\">".$matches[3][$index]."</a>", $html);
        }
    }
    $html = preg_replace("/<\/?w[^>]*>/", '', $html);

    // divineName
    $html = preg_replace("/<divineName[^>]*>/", '<span class="bible-divineName">', $html);
    $html = preg_replace("/<\/divineName\s*>/", '</span>', $html);

    // resp (includes Strong's tags)
    $html = preg_replace("/<resp [^>]*>/", '', $html);

    // q who="Jesus" (words of Christ in red)
    $html = preg_replace("/<q ([^>]*)who=\"Jesus\"\s*>/", '<span class="bible-words-Christ">', $html);
    $html = preg_replace("/<\/q\s*>/", '</span>', $html);

    // transChange
    $html = preg_replace("/<transChange[^>]*>/", '<i>', $html);
    $html = preg_replace("/<\/transChange\s*>/", '</i>', $html);

    // milestone type="x-p"
    $html = preg_replace("/<milestone ([^>]*)type=\"x-p\"\s*\/>/", '<br />', $html);

    return $html;

} 

?>
