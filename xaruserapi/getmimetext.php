<?php
/**
* Get list of text-based mimetypes
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Get list of text-based mimetypes
*
* @author Curtis Farnham <curtis@farnham.com>
* @access  public
* @return  array
* @returns item array
* @todo <curtisdf> make this user-editable (?)
*/
function files_userapi_getmimetext()
{
    // NOTE: The mime module has TONS of mimetypes and I don't know
    // which of all of them should be editable as text.  So contact
    // me if you see any mistakes.

    $mimetext = array();
    $mimetext[] = 'application/xhtml+xml';
    $mimetext[] = 'application/x-httpd-php3';
    $mimetext[] = 'application/x-httpd-php';
    $mimetext[] = 'application/x-javascript';
    $mimetext[] = 'application/xml';
    $mimetext[] = 'application/x-sh'; // shell script?
    $mimetext[] = 'audio/x-toc'; // cdrdao TOC files?
    $mimetext[] = 'audio/x-voc'; // video version of cdrdao TOC files?
    $mimetext[] = 'text/calendar';
    $mimetext[] = 'text/cpp';
    $mimetext[] = 'text/css';
    $mimetext[] = 'text/diff';
    $mimetext[] = 'text/html';
    $mimetext[] = 'text/plain';
    $mimetext[] = 'text/richtext';
    $mimetext[] = 'text/rtf';
    $mimetext[] = 'text/script';
    $mimetext[] = 'text/sgml';
    $mimetext[] = 'text/tab-separated-values';
    $mimetext[] = 'text/vnd.ms-word';
    $mimetext[] = 'text/vnd.wap.wml';
    $mimetext[] = 'text/vnd.wap.wmlscript';
    $mimetext[] = 'text/vnd.wordperfect';
    $mimetext[] = 'text/x-apple-binscii'; // does "binscii" imply binary?
    $mimetext[] = 'text/xml';
    $mimetext[] = 'text/x-patch';
    $mimetext[] = 'text/x-setext';
    $mimetext[] = 'text/x-vcard';

    return $mimetext;
}

?>
