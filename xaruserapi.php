<?php
/**
* User API functions
*
* @package unassigned
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* highlight text
*
* Transform text, intelligently sorting out which input args need it
*
* @author  Curtis Farnham <curtis@farnham.com>
* @access  public
* @param   array $args['extrainfo'] text to transform, OR
* @param   string $args text to transform
* @return  string or array of transformed text items
* @throws  BAD_PARAM
*/
function highlight_userapi_transform($args)
{
    // Get arguments from argument array
    extract($args);

    // validate inputs
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    /** $extrainfo can be a string or an array! **/

    // if extrainfo is an array...
    if (is_array($extrainfo)) {

        // and we're given a list of which elements to transform...
        if (isset($extrainfo['transform']) &&
            is_array($extrainfo['transform'])) {

            // scroll through the list and transform each element
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = xarModAPIFunc(
                        'highlight', 'user', '_transform', $extrainfo[$key]
                    );
                }
            }

            // success
            return $extrainfo;

        // and we're not told which elements to transform...
        } else {

            // transform each element in the array
            $transformed = array();
            foreach($extrainfo as $key => $text) {
                $transformed[$key] = xarModAPIFunc(
                    'highlight', 'user', '_transform', $text
                );
            }
        }

    // or if $extrainfo is not an array...
    } else {

        // assume it's text and transform it
        $transformed = xarModAPIFunc(
            'highlight', 'user', '_transform', $extrainfo
        );
    }

    // success
    return $transformed;
}


/**
* transform text
*
* @author  Curtis Farnham <curtis@farnham.com>
* @access  private
* @param   string $text text to transform
* @return  transformed text
* @throws  BAD_PARAM
*/
function highlight_userapi__transform($text)
{
    // if text is empty or there aren't even any HTML tags, move on
    if (!strstr($text, '<') || empty($text)) {
        return $text;
    }

    // retrieve the code attribute
    $string = xarModGetVar('highlight', 'string');

    // if string is empty, we can't continue
    if (empty($string)) {
        return $text;
    }

    // assemble regexp matching strings
    $att = '\s+[a-zA-Z]+=\"[^\"]+\"';
    $attstr = '\s+'.preg_quote($string, '/').'=\"([^\"]+)\"';

    // find starting position for all segments that need highlighting
    preg_match_all(
        "/<([a-zA-Z]+)($att)*($attstr)($att)*>/",
        $text,
        $matches,
        PREG_OFFSET_CAPTURE
    );

    // if nothing was flagged for highlighting, move on
    if (empty($matches[0])) return $text;

    // get list of HTML items to transform
    $trans_tbl = array_flip(get_html_translation_table(HTML_ENTITIES));

    // retrieve transform utility class
    include_once('modules/highlight/xarclasses/geshi.php');

    // initialize highlighting utility
    $geshi = new GeSHi('', 'php');
    $geshi->set_header_type(GESHI_HEADER_PRE);
    $geshi->set_link_target('_blank');

    // perform the transformation on each segment
    $replace = array();
    foreach ($matches[1] as $matchno => $matchdata) {

        // prepare to retrieve end position
        list($tag, $offset) = $matchdata;

        // calculate starting and ending positions for current segment
        $start = $offset + strlen($matches[0][$matchno][0]) - 1;
        preg_match("/<\/$tag>/", $text, $match, PREG_OFFSET_CAPTURE, $start);
        $end = $match[0][1];

        // get the segment we want to highlight
        $section = substr($text, $start, $end - $start);

        // convert HTML entities to real characters
        $section = strtr($section, $trans_tbl);

        // perform highlighting
        $geshi->set_source($section);
        $geshi->set_language($matches[4][$matchno][0]);
        $section = $geshi->parse_code();

        // log this segment's starting and ending positions for later
        $replace[] = array(
            'section' => $section,
            'start' => $start,
            'end' => $end
        );
    }

    /**
    * Replace original code sections with highlighted versions.  Note that
    * this changes the length of the string and therefore we have to work
    * from the end to the beginning of the string to avoid interfering with
    * our offsets.
    */
    foreach (array_reverse($replace) as $row) {
        $text = substr($text, 0, $row['start']-1)
            . $row['section']
            . substr($text, $row['end']);
    }

    // success
    return $text;
}

?>
