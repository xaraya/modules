<?php
/**
* Get languages
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Get languages
*
* Return a list of languages this module can highlight
*
* @author  Curtis Farnham <curtis@farnham.com>
* @access  public
* @return  array
* @returns list of possible languages
*/
function highlight_userapi_getlanguages($args)
{
    extract($args);

    // retrieve and initialize highlighter utility
    include_once('modules/highlight/xarclasses/geshi.php');
    $geshi = new GeSHi('', 'php');

    // get directory where language files are stored
    $dir = $geshi->language_path;

    // open language directory and scan for languages
    $hd = opendir($dir);
    $languages = array();
    while (false !== ($file = readdir($hd))) {

        // only process PHP files
        if (!preg_match("/\.php\$/", $file)) {
            continue;
        }

        // get shortname of language
        $lang = preg_replace("/\.php\$/", '', $file);

        // protect against extra spaces at top and bottom of language files
        // (there are at least a few files like this...)
        ob_start();
        $geshi->set_language($lang);
        ob_end_clean();

        // get displayname of language
        $languages[$lang] = $geshi->language_data['LANG_NAME'];
    }

    // sort languages by displayname, preserving keys
    asort($languages);

    return $languages;
}

?>
