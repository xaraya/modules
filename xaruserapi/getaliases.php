<?php
/**
 * File: $Id:
 * 
 * Get book aliases
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
 * get book aliases
 * 
 * @author curtisdf 
 * @returns array
 * @return array of alias data, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_getaliases($args)
{ 
    extract($args); 

    if (empty($groups)) $groups = '';
    if (empty($type)) $type = '';

    // security check
    if (!xarSecurityCheck('ViewBible')) return; 

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables(); 

    $aliastable = $xartable['bible_aliases']; 

    switch($type) {
    case 'display': // get only sword and display columns

        $query = "SELECT xar_sword, xar_display FROM $aliastable";
        $result = $dbconn->Execute($query);

        if (!$result) return; 

        $aliases = array();
        for (; !$result->EOF; $result->MoveNext()) {
            list($sword, $display) = $result->fields;
            $aliases[$sword] = $display;
        }
        $result->Close(); 

        return array($aliases);

        break;
    case 'groups': // get list of swordbooks according to group

        // list the Sword names of the books in each group
        $swordgroups = array(

        // Old Testament
        'ot' => array('Genesis', 'Exodus', 'Leviticus', 'Numbers', 'Deuteronomy', 'Joshua', 'Judges', 'Ruth', 'I Samuel', 'II Samuel', 'I Kings', 'II Kings', 'I Chronicles', 'II Chronicles', 'Ezra', 'Nehemiah', 'Esther', 'Job', 'Psalms', 'Proverbs', 'Ecclesiastes', 'Song of Solomon', 'Isaiah', 'Jeremiah', 'Lamentations', 'Ezekiel', 'Daniel', 'Hosea', 'Joel', 'Amos', 'Obadiah', 'Jonah', 'Micah', 'Nahum', 'Habakkuk', 'Zephaniah', 'Haggai', 'Zechariah', 'Malachi'),

        // New Testament
        'nt' => array('Matthew', 'Mark', 'Luke', 'John', 'Acts', 'Romans', 'I Corinthians', 'II Corinthians', 'Galatians', 'Ephesians', 'Philippians', 'Colossians', 'I Thessalonians', 'II Thessalonians', 'I Timothy', 'II Timothy', 'Titus', 'Philemon', 'Hebrews', 'James', 'I Peter', 'II Peter', 'I John', 'II John', 'III John', 'Jude', 'Revelation'),

        // Books of Law
        'law' => array('Genesis', 'Exodus', 'Leviticus', 'Numbers', 'Deuteronomy'),

        // OT History
        'othist' => array('Joshua', 'Judges', 'Ruth', 'I Samuel', 'II Samuel', 'I Kings', 'II Kings', 'I Chronicles', 'II Chronicles', 'Ezra', 'Nehemiah', 'Esther'),

        // Wisdom
        'wis' => array('Job', 'Psalms', 'Proverbs', 'Ecclesiastes', 'Song of Solomon'),

        // Major Prophets
        'majpro' => array('Isaiah', 'Jeremiah', 'Lamentations', 'Ezekiel', 'Daniel'),

        // Minor Prophets
        'minpro' => array('Hosea', 'Joel', 'Amos', 'Obadiah', 'Jonah', 'Micah', 'Nahum', 'Habakkuk', 'Zephaniah', 'Haggai', 'Zechariah', 'Malachi'),

        // Gospels
        'gosp' => array('Matthew', 'Mark', 'Luke', 'John'),

        // NT History
        'nthist' => array('Matthew', 'Mark', 'Luke', 'John', 'Acts'),

        // Epistles
        'epis' => array('Romans', 'I Corinthians', 'II Corinthians', 'Galatians', 'Ephesians', 'Philippians', 'Colossians', 'I Thessalonians', 'II Thessalonians', 'I Timothy', 'II Timothy', 'Titus', 'Philemon', 'Hebrews', 'James', 'I Peter', 'II Peter', 'I John', 'II John', 'III John', 'Jude', 'Revelation'),

        // Apocalyptic
        'apoc' => array('Daniel', 'Revelation')

        );

        // define group names
        $groupnames = array('ot' => xarML('Old Testament'),
                            'nt' => xarML('New Testament'),
                            'law' => xarML('Books of Law'),
                            'othist' => xarML('OT History'),
                            'wis' => xarML('Books of Wisdom'),
                            'majpro' => xarML('Major Prophets'),
                            'minpro' => xarML('Minor Prophets'),
                            'gosp' => xarML('Gospels'),
                            'nthist' => xarML('NT History'),
                            'epis' => xarML('Epistles'),
                            'apoc' => xarML('Apocalyptic Books')
                           );

        // map potential user-entered groups to the group we are using
        $aliases = array('ot' => &$swordgroups['ot'],
                         'nt' => &$swordgroups['nt'],
                         'law' => &$swordgroups['law'],
                         'othist' => &$swordgroups['othist'],
                         'othistory' => &$swordgroups['othist'],
                         'wis' => &$swordgroups['wis'],
                         'wisdom' => &$swordgroups['wis'],
                         'majpro' => &$swordgroups['majpro'],
                         'majprophets' => &$swordgroups['majpro'],
                         'maj' => &$swordgroups['majpro'],
                         'major' => &$swordgroups['majpro'],
                         'minpro' => &$swordgroups['minpro'],
                         'minprophets' => &$swordgroups['minpro'],
                         'min' => &$swordgroups['minpro'],
                         'minor' => &$swordgroups['minpro'],
                         'gosp' => &$swordgroups['gosp'],
                         'gospel' => &$swordgroups['gosp'],
                         'gospels' => &$swordgroups['gosp'],
                         'gos' => &$swordgroups['gosp'],
                         'nthist' => &$swordgroups['nthist'],
                         'nthistory' => &$swordgroups['nthist'],
                         'epis' => &$swordgroups['epis'],
                         'ep' => &$swordgroups['epis'],
                         'epistles' => &$swordgroups['epis'],
                         'apoc' => &$swordgroups['apoc'],
                         'ap' => &$swordgroups['apoc'],
                         'apocalypse' => &$swordgroups['apoc'],
                         'apocalyptic' => &$swordgroups['apoc'],
                         'endtimes' => &$swordgroups['apoc'],
                         'end' => &$swordgroups['apoc'],
                         'et' => &$swordgroups['apoc']
                        );

        return array($aliases, $groupnames);

        break;
    default: // get everything from table

        $query = "SELECT * FROM $aliastable";
        $result = $dbconn->Execute($query);

        if (!$result) return; 

        $aliases = array();
        for (; !$result->EOF; $result->MoveNext()) {
            list($aid, $sword, $display, $aliasline) = $result->fields;
            $aliases[] = array($aid, $sword, $display, $aliasline);
        }
        $result->Close(); 

        // Return the texts
        return $aliases;

    }


} 

?>
