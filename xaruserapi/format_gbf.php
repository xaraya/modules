<?php
/**
 * File: $Id:
 *
 * Format a string for GBF format
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
 * format GBF string
 *
 * @author curtisdf
 * @param  $args ['html'] results array
 * @param  $args ['strongs'] boolean, whether or not to format with strong's numbers
 * @returns string
 * @return formatted string, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_format_gbf($args)
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

    /* Text type tags */
    $html = preg_replace("/<H\w\d?>/", '', $html);

    /* File body tags */
    $html = preg_replace("/<B\w>/", '', $html);

    /* File tail tags */
    $html = preg_replace("/<Z\w>/", '', $html);

    /* Section headers, book titles, etc. */

    // Psalm Book Title
    $html = preg_replace("/<TB>/", '<div class="bible-psalm-book-title">', $html);
    $html = preg_replace("/<Tb>/", '</div>', $html);

    // Hebrew Title
    $html = preg_replace("/<TH>/", '<div class="bible-hebrew-title">', $html);
    $html = preg_replace("/<Th>/", '</div>', $html);

    // Hebrew Title
    $html = preg_replace("/<TS>/", '<div class="bible-section-header">', $html);
    $html = preg_replace("/<Ts>/", '</div>', $html);

    // Book Title
    $html = preg_replace("/<TT>/", '<div class="bible-book-title">', $html);
    $html = preg_replace("/<Tt>/", '</div>', $html);

    // Short Book Name
    $html = preg_replace("/<TN>/", '<div class="bible-short-book-name">', $html);
    $html = preg_replace("/<Tn>/", '</div>', $html);

    // Vernacular Book Abbreviation
    $html = preg_replace("/<TA>/", '<div class="bible-vernacular-book-abbreviation">', $html);
    $html = preg_replace("/<Ta>/", '</div>', $html);

    // Vernacular Book Abbreviation
    $html = preg_replace("/<TP>/", '<div class="bible-preface">', $html);
    $html = preg_replace("/<Tp>/", '</div>', $html);

    /* Font Attributes */

    // Bold
    $html = preg_replace("/<FB>/", '<span class="bible-bold">', $html);
    $html = preg_replace("/<Fb>/", '</span>', $html);

    // Small caps
    $html = preg_replace("/<FC>/", '<span class="bible-smallcaps">', $html);
    $html = preg_replace("/<Fc>/", '</span>', $html);

    // Italics
    $html = preg_replace("/<FI>/", '<span class="bible-italic">', $html);
    $html = preg_replace("/<Fi>/", '</span>', $html);

    // Font name
    preg_match_all("/<FN([^>]*)>/", $html, $matches);
    foreach ($matches[0] as $index => $match) {
        $html = str_replace($match, '<span style="font-family: \''.$matches[1][$index].'\';">', $html);
    }
    $html = preg_replace("/<Fn>/", '</span>', $html);

    // Old Testament Quote
    $html = preg_replace("/<FI>/", '<div class="bible-old-testament-quote">', $html);
    $html = preg_replace("/<Fi>/", '</div>', $html);

    // Red (words of Jesus)
    $html = preg_replace("/<FR>/", '<span class="bible-red">', $html);
    $html = preg_replace("/<Fr>/", '</span>', $html);

    // Superscript
    $html = preg_replace("/<FS>/", '<span class="bible-superscript">', $html);
    $html = preg_replace("/<Fs>/", '</span>', $html);

    // Underline
    $html = preg_replace("/<FU>/", '<span class="bible-superscript">', $html);
    $html = preg_replace("/<Fu>/", '</span>', $html);

    // Subscript
    $html = preg_replace("/<FV>/", '<span class="bible-superscript">', $html);
    $html = preg_replace("/<Fv>/", '</span>', $html);

    /* Paragraph Attributes */

    $html = preg_replace("/<P[^Ii]>/", '', $html);

    // Indented quote
    $html = preg_replace("/<PI>/", '<div class="bible-indented-quote">', $html);
    $html = preg_replace("/<Pi>/", '</div>', $html);


    // Text with an embedded footnote
    preg_match_all("/<RB>(.*)<RF>(.*)<Rf>/", $html, $matches);
    foreach ($matches[0] as $index => $match) {
        $html = str_replace($match, '<span class="bible-further-described">'.$matches[1][$index].'</span><span class="bible-link-to-footnote">'.$matches[2][$index].'</span>');
    }
    $html = preg_replace("/<RF>/", '<span class="bible-link-to-footnote">', $html);
    $html = preg_replace("/<Rf>/", '</span>', $html);
    $html = preg_replace("/<RB>/", '', $html);

    // Parallel Passage
    $html = preg_replace("/<R[Pp][^>]*>/", '', $html);

    // Cross reference
    $html = preg_replace("/<R[Xx][^>]*>/", '', $html);

    /* Word Information Tags */
    if ($strongs) {
        preg_match_all("/(\w+([^\w ]\w+)?)<W([GH])(\d+)>/", $html, $matches);
        foreach ($matches[0] as $index => $match) {
            if ($matches[1][$index] == 'G') {
                $sname = 'StrongsGreek';
            } elseif ($matches[1][$index] == 'H') {
                $sname = 'StrongsHebrew';
            }
            $url = xarModURL('bible', 'user', 'dictionary', array('sname' => $sname, 'query' => $matches[2][$index], 'string' => $matches[3][$index]));
            $html = str_replace($match, "<a href=\"$url\">".$matches[1][$index]."</a>");
        }
    }
    $html = preg_replace("/<W\w[^>]*>/", '', $html);

    /* Sync Marks */
    $html = preg_replace("/<S\w[^>]*>/", '', $html);

    /* Special Character Tags */

    // ASCII character
    preg_match_all("/<CA(\d\d)>/", $html, $matches);
    foreach ($matches[0] as $index => $match) {
        $html = str_replace($match, '&#'.$matches[1][$index].';');
    }

    // greater-than sign
    $html = preg_replace("/<CG>/", '&gt;', $html);

    // less-than sign
    $html = preg_replace("/<CT>/", '&lt;', $html);

    // End of Paragraph
    $html = preg_replace("/<CM>/", '<br />', $html);

    // End of Line
    $html = preg_replace("/<CL>/", '<br />&nbsp; &nbsp; ', $html);

    // Unicode character
    $html = preg_replace("/<CU\d\d\d\d>/", '', $html);

    return $html;

}

?>
