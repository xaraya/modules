<?php
/**
 * Xaraya converter
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage converter Module
 * @author John Cox
*/
/**
 * Admin function
 */
function converter_adminapi_pntheme($args)
{
    define('_EDIT','Edit');
    define('_DELETE','Delete');

    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($theme)) ||
        (!isset($theme_dir))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $theme_file = $theme_dir.'theme.php';
    if (!file_exists($theme_file)) {
        $msg = xarML('Theme file does not exist.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $file = join('', file($theme_file));
    $file = str_replace("\"\n", "'\n", $file);
    $file = str_replace('".', "'.", $file);
    $file = str_replace('";', "';", $file);
    $file = str_replace('."', ".'", $file);
    $file = str_replace('\"', '"', $file);
    $file = str_replace('echo "', "echo '", $file);
    $file = str_replace('\n', '\'."\n".\'', $file);
    $file = str_replace(".''", '', $file);
    $file = str_replace('$thename', $theme, $file);
    $file = str_replace('pnSecAuthAction(0, \'Stories::Story\', "$info[aid]:$info[cattitle]:$info[sid]", ACCESS_DELETE)', '', $file);
    $file = str_replace('pnSecAuthAction(0, \'Stories::Story\', "$info[aid]:$info[cattitle]:$info[sid]", ACCESS_EDIT)', '', $file);
    $file = str_replace('_SEARCH', '<xar:mlstring>Search</xar:mlstring>', $file);
    $file = str_replace('../themes', "#xarConfigGetVar('Site.BL.ThemesDirectory')#", $file);
    $file = str_replace('user.php', 'index.php?module=roles&func=account', $file);
    $file = str_replace('_READS', '#$counter# <xar:mlstring>Reads</xar:mlstring>', $file);
    $file = str_replace('_POSTEDBY', '<xar:mlstring>Posted By</xar:mlstring>', $file);
    $file = str_replace('_POSTED', '<xar:mlstring>Posted</xar:mlstring>', $file);
    $file = str_replace('_ON', '<xar:mlstring>On</xar:mlstring>', $file);
    $file = str_replace('_EDIT', '<xar:if condition="!empty($editurl)"><a href="#$editurl#">#$edittitle#</a></xar:if>', $file);
    $file = str_replace('_DELETE', 'Delete', $file);
    $file = str_replace('_PUBLISHED', 'Published', $file);
    $file = preg_replace('/opentable\(/i', 'pnOpenTable(', $file);
    $file = preg_replace('/opentable2\(/i', 'pnOpenTable2(', $file);
    $file = preg_replace('/closetable\(/i', 'pnCloseTable(', $file);
    $file = preg_replace('/closetable2\(/i', 'pnCloseTable2(', $file);

    $fp = fopen($theme_dir . 'temp.php', 'w+');
    fwrite($fp, $file);
    fclose($fp);

    $file2 = join('', file($theme_file));
    $file2 = preg_replace('/opentable\(/i', 'pnOpenTable(', $file2);
    $file2 = preg_replace('/opentable2\(/i', 'pnOpenTable2(', $file2);
    $file2 = preg_replace('/closetable\(/i', 'pnCloseTable(', $file2);
    $file2 = preg_replace('/closetable2\(/i', 'pnCloseTable2(', $file2);
    $file2 = preg_replace('/blocks\(\'left\'\)/i', 'echo \'<xar:blockgroup name="left" id="left" />\';', $file2);
    $file2 = preg_replace('/blocks\(\'centre\'\)/i', 'echo \'<xar:blockgroup name="center" id="center" />\';', $file2);
    $file2 = preg_replace('/blocks\(\'right\'\)/i', 'echo \'<xar:blockgroup name="right" id="right" />\';', $file2);

    $new_theme_file = $theme_dir . 'temptheme.php';
    $fp2 = fopen($new_theme_file, 'w+');
    fwrite($fp2, $file2);
    fclose($fp2);

    // bring out right and center blocks
    global $index;
    $index = 1;

    global $thename;
    $thename = $theme;

    $new_lang_file = $theme_dir . 'lang/eng/global.php';
    if (file_exists($new_lang_file)) {
        include $new_lang_file;
    }
    include $new_theme_file;

    if (!file_exists($theme_dir.'pages')) {
        mkdir($theme_dir.'pages', 0777);
    }

    if (!file_exists($theme_dir.'blocks')) {
        mkdir($theme_dir.'blocks', 0777);
    }

    if (!file_exists($theme_dir.'modules')) {
        mkdir($theme_dir.'modules', 0777);
    }

    if (!file_exists($theme_dir.'modules/articles')) {
        mkdir($theme_dir.'modules/articles', 0777);
    }

    // Main Header
    $fp = fopen($theme_dir.'pages/default.xt', 'w');
    // get header output
    ob_start();
    converter_adminapi_simulateheader($theme);
    themeheader();
    $output = ob_get_contents();
    ob_end_clean();

    fwrite($fp, $output);
    fwrite($fp, "\n".'<xar:module main="true" />'."\n");
    // get footer output
    ob_start();
    themefooter();
    $output = ob_get_contents();
    // Common Links
    ob_end_clean();
    fwrite($fp, $output.
                "</body>\n</html>\n</xar:blocklayout>\n");
    fclose($fp);


    // Left Blocks
    $fp = fopen($theme_dir . 'temp.php', 'r');
    $fp = fopen($theme_dir.'blocks/default.xt', 'w');
    ob_start();
    themesidebox(array('position' => 'l',
                       'title' => '#$title#',
                       'content' => '#$content#'));
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);

    // Right Block
    $fp = fopen($theme_dir.'blocks/right.xt', 'w');
    ob_start();
    themesidebox(array('position' => 'r',
                       'title' => '#$title#',
                       'content' => '#$content#'));
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);


    // Header Block
    $fp = fopen($theme_dir.'blocks/header.xt', 'w');
    $output = '#$content#';
    fwrite($fp, $output);
    fclose($fp);

    // News Summary
    $fp = fopen($theme_dir.'modules/articles/user-summary-news.xt', 'w');
    ob_start();
    converter_adminapi_articleviewtop();

    // TODO add categories to template
    $catandtitle = '<a class="xar-title" href="#$link#">#$title#</a>';
/*
    $catandtitle =
'<xar:if condition="!empty($categories) and count($categories) gt 0">
    [ <xar:loop name="$categories">
           #$cjoin# <a href="#$clink#">#$cname#</a>
      </xar:loop> ]
</xar:if>
<a class="xar-title" href="#$link#">#$title#</a>';
*/
     $info = array('informant' => '#$author#',
                   'longdatetime' => '#$date#',
                   'briefdatetime' => '#$date#',
                   'aid' => '#$aid#',
                   'sid' => '#$sid#',
                   'cattitle' => '#$cattitle#',
                   'hometext' => '#$summary#');
     $links = NULL;
     $preformat = array('notes' => '#$notes#',
                        'more' => '#$more#',
                        'fulltext' => '#$fulltext#',
                        'searchtopic' => '#$searchtopic#',
                        'send' =>  '#$send#',
                        'print' => '#$print#',
                        'catandtitle' => $catandtitle);
     $_deprecated='';
    themeindex ($_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $info, $links, $preformat);
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);


    // Article Display Override
    $fp = fopen($theme_dir.'modules/articles/user-display-news.xt', 'w');
    ob_start();
    converter_adminapi_fullarticletop();
    $catandtitle = '#$title#';
    $fulltext = '<p>#$summary#</p>
                 <p>#$body#</p>
                 <xar:if condition="!empty($notes)">
                    <p><xar:mlstring>Note</xar:mlstring> : <i>#$notes#</i></p>
                 </xar:if>';

     $info = array('informant' => '#$author#',
                   'longdatetime' => '#$date#',
                   'briefdatetime' => '#$date#',
                   'aid' => '#$aid#',
                   'sid' => '#$sid#',
                   'cattitle' => '#$cattitle#',
                   'hometext' => '#$summary#');

     $links = NULL;

     $preformat = array('notes' => '#$notes#',
                        'more' => '#$more#',
                        'searchtopic' => '#$searchtopic#',
                        'fulltext' => $fulltext,
                        'send' =>  '#$send#',
                        'print' => '#$print#',
                        'catandtitle' => $catandtitle);

     $_deprecated='';

    themearticle ($_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $info, $links, $preformat);

    converter_adminapi_fullarticlebottom();
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);

    // Add Xaraya Required CSS
    $fp = fopen($theme_dir.'style/xarstyle.css', 'w');
    ob_start();
    converter_adminapi_addxarcss();
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);

    return NULL;
}

function converter_adminapi_pnuketheme($args)
{

    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($theme)) ||
        (!isset($theme_dir))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!file_exists($theme_file)) {
        $msg = xarML('Theme file does not exist.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Have to take a different approach with phpnuke themes.
    /*
    $theme_file = $theme_dir.'theme.php';

    $file = join('', file($theme_file));
    $file = str_replace("\"\n", "'\n", $file);
    $file = str_replace('".', "'.", $file);
    $file = str_replace('";', "';", $file);
    $file = str_replace('."', ".'", $file);
    $file = str_replace('\"', '"', $file);
    $file = str_replace('echo "', "echo '", $file);
    $file = str_replace('\n', '\'."\n".\'', $file);
    $file = str_replace(".''", '', $file);
    $file = str_replace('$thename', $theme, $file);
    $file = str_replace('../themes', "#xarConfigGetVar('Site.BL.ThemesDirectory')#", $file);
    $file = str_replace('_SEARCH', '<xar:mlstring>Search</xar:mlstring>', $file);

    // Dirty little hack to create a new function in the temp file.
    $file = str_replace('$public_msg = public_message();', '$public_msg = public_message();} xarThemeTemp{', $file);


    $fp = fopen($theme_dir . 'temp.php', 'w+');
    fwrite($fp, $file);
    fclose($fp);
    */


    // bring out right and center blocks

    if (!file_exists($theme_dir.'pages')) {
        mkdir($theme_dir.'pages', 0777);
    }

    if (!file_exists($theme_dir.'blocks')) {
        mkdir($theme_dir.'blocks', 0777);
    }

    if (!file_exists($theme_dir.'modules')) {
        mkdir($theme_dir.'modules', 0777);
    }

    if (!file_exists($theme_dir.'modules/articles')) {
        mkdir($theme_dir.'modules/articles', 0777);
    }

    // Main Header
    $fp = fopen($theme_dir.'pages/default.xt', 'w');
    // get header output
    ob_start();
    converter_adminapi_simulateheader($theme);
    converter_adminapi_themeheadersimulate($theme);
    $output = ob_get_contents();
    ob_end_clean();

    fwrite($fp, $output);
    fwrite($fp, "\n".'<xar:module main="true" />'."\n");

    if (file_exists(xarConfigGetVar('Site.BL.ThemesDirectory').'/$theme/header.html')){
    return 'file exists';
    }

    // get footer output
    ob_start();
    converter_adminapi_themefootersimulate($theme);
    $output = ob_get_contents();
    // Common Links
    ob_end_clean();
    fwrite($fp, $output."</body>\n</html>\n");
    fclose($fp);

    // Left Blocks
    $fp = fopen($theme_dir . 'temp.php', 'r');
    $fp = fopen($theme_dir.'blocks/default.xt', 'w');
    ob_start();
    $title = '#$title#';
    $content = '#$content#';
    converter_adminapi_themeblocksimulate($theme, $title. $content);
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);

    // Right Block
    $fp = fopen($theme_dir.'blocks/right.xt', 'w');
    ob_start();
    $title = '#$title#';
    $content = '#$content#';
    converter_adminapi_themeblocksimulate($theme, $title. $content);
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);


    // Header Block
    $fp = fopen($theme_dir.'blocks/header.xt', 'w');
    $output = '#$content#';
    fwrite($fp, $output);
    fclose($fp);
}
/*
    // News Summary
    $fp = fopen($theme_dir.'modules/articles/user-summary-news.xt', 'w');
    ob_start();
    converter_adminapi_articleviewtop();

    // TODO add categories to template
    $catandtitle = '<a class="xar-title" href="#$link#">#$title#</a>';
//    $catandtitle = '<xar:if condition="!empty($categories) and count($categories) gt 0">
//                    [ <xar:loop name="$categories">
//                        #$cjoin# <a href="#$clink#">#$cname#</a>
//                    </xar:loop> ]
//                </xar:if>
//              <a class="xar-title" href="#$link#">#$title#</a>';

     $info = array('informant' => '#$author#',
                   'longdatetime' => '#$date#',
                   'briefdatetime' => '#$date#',
                   'aid' => '#$aid#',
                   'sid' => '#$sid#',
                   'cattitle' => '#$cattitle#',
                   'hometext' => '#$summary#');
     $links = NULL;
     $preformat = array('notes' => '#$notes#',
                        'more' => '#$more#',
                        'fulltext' => '#$fulltext#',
                        'searchtopic' => '#$searchtopic#',
                        'send' =>  '#$send#',
                        'print' => '#$print#',
                        'catandtitle' => $catandtitle);
     $_deprecated='';
    themeindex ($_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $info, $links, $preformat);
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);


    // Article Display Override
    $fp = fopen($theme_dir.'modules/articles/user-display-news.xt', 'w');
    ob_start();
    converter_adminapi_fullarticletop();
    $catandtitle = '#$title#';
    $fulltext = '<p>#$summary#</p>
                 <p>#$body#</p>
                 <xar:if condition="!empty($notes)">
                    <p><xar:mlstring>Note</xar:mlstring> : <i>#$notes#</i></p>
                 </xar:if>';

     $info = array('informant' => '#$author#',
                   'longdatetime' => '#$date#',
                   'briefdatetime' => '#$date#',
                   'aid' => '#$aid#',
                   'sid' => '#$sid#',
                   'cattitle' => '#$cattitle#',
                   'hometext' => '#$summary#');

     $links = NULL;

     $preformat = array('notes' => '#$notes#',
                        'more' => '#$more#',
                        'searchtopic' => '#$searchtopic#',
                        'fulltext' => $fulltext,
                        'send' =>  '#$send#',
                        'print' => '#$print#',
                        'catandtitle' => $catandtitle);

     $_deprecated='';

    themearticle ($_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $_deprecated, $info, $links, $preformat);

    converter_adminapi_fullarticlebottom();
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);

    // Add Xaraya Required CSS
    $fp = fopen($theme_dir.'style/xarstyle.css', 'w');
    ob_start();
    converter_adminapi_addxarcss();
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);

    return NULL;
}
*/
function converter_adminapi_createxarthemefile($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($theme)) ||
        (!isset($id))    ||
        (!isset($author))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Create xartheme.php File
    $fp = fopen($theme_dir.'xartheme.php', 'w');
    ob_start();
    converter_adminapi_createxartheme($theme, $id, $author);
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);

    return NULL;
}

function converter_adminapi_createxartheme($theme, $id, $author)
{

$date = time();

    echo "<?php\n";
    echo '$themeinfo[\'name\'] ='."'$theme';\n";
    echo '$themeinfo[\'id\'] ='."'$id';\n";
    echo '$themeinfo[\'directory\'] ='."'$theme';\n";
    echo '$themeinfo[\'author\'] ='."'$author';\n";
    echo '$themeinfo[\'homepage\'] ='."'$author homepage';\n";
    echo '$themeinfo[\'email\'] ='."'$author email';\n";
    echo '$themeinfo[\'description\'] ='."'$theme';\n";
    echo '$themeinfo[\'contact_info\'] ='."'$author';\n";
    echo '$themeinfo[\'publish_date\'] ='."'$date';\n";
    echo '$themeinfo[\'license\'] ='."' GPL';\n";
    echo '$themeinfo[\'version\'] ='."' 1.0';\n";
    echo '$themeinfo[\'xar_version\'] ='."' 1.0';\n";
    echo '$themeinfo[\'bl_version\'] ='."' 1.0';\n";
    echo '$themeinfo[\'class\'] ='."' 2';\n";
    echo "?>";
}


function converter_adminapi_articleviewtop()
{

echo '<div class="xar-alt" style="margin: 0px 3px 6px 3px;padding: 1px;text-align: left;">'."\n";
echo '<div class="xar-norm" style="padding: 3px;">'."\n";
/*
echo '<xar:categories-navigation layout="trails" showchildren="1" module="articles" itemtype="$ptid" catid="$catid" />'."\n";
echo '</xar:if>'."\n\n";
*/
}

function converter_adminapi_simulateheader($theme)
{

    global
    $index,
        $artpage,
        $topic,
        $hlpfile,
        $hr,
        $theme,
        $bgcolor1,
        $bgcolor2,
        $bgcolor3,
        $bgcolor4,
        $bgcolor5,
        $textcolor1,
        $textcolor2,
        $textcolor3,
        $textcolor4,
        $forumpage,
        $thename,
        $postnuke_theme,
        $pntheme,
        $themename,
        $themeimages,
        $additional_header;

    echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
    echo "<?xar type=\"page\" ?>\n";
    echo "<xar:blocklayout version=\"1.0\" content=\"text/html\" xmlns:xar=\"http://xaraya.com/2004/blocklayout\">\n";
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
    echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
    echo "<head>\n";
    echo '<title>#$tpl:pageTitle#</title> '."\n";
    echo '<xar:blockgroup name="header" id="header" /> '."\n";
    echo '<base href="&xar-baseurl;index.php" /> '."\n";
    echo "<style type=\"text/css\">";
    echo "@import url(\"&xar-baseurl;#xarConfigGetVar('Site.BL.ThemesDirectory')#/$theme/style/style.css\"); ";
    echo "</style>\n";
    echo "<style type=\"text/css\">";
    echo "@import url(\"&xar-baseurl;#xarConfigGetVar('Site.BL.ThemesDirectory')#/$theme/style/xarstyle.css\"); ";
    echo "</style>\n";
    echo "<xar:comment> Head JavaScript </xar:comment>\n";
    echo "<xar:base-render-javascript position=\"head\" />\n\n";
}

function converter_adminapi_fullarticletop()
{
    echo '<xar:template file="publinks" type="module" /><p /><xar:categories-navigation layout="trails" showchildren="1" module="articles" itemtype="$ptid" itemid="$aid" cids="$cids" />';
}

function converter_adminapi_fullarticlebottom()
{
    echo '<xar:if condition="!empty($prevart) || !empty($nextart)">
    <br />
    <table border="0" width="95%">
    <tr>
        <td align="left"><xar:if condition="!empty($prevart)"><a href="#$prevart#">&lt; <xar:mlstring>prev</xar:mlstring> </a></xar:if>&nbsp;</td>
        <td align="right">&nbsp;<xar:if condition="!empty($nextart)"> <a href="#$nextart#"><xar:mlstring>next</xar:mlstring> &gt;</a></xar:if></td>
    </tr>
    </table>
    </xar:if>

    <xar:if condition="!empty($hooks)">
    <br />
    <xar:foreach in="$hooks" key="$hookmodule">
    #$hooks[$hookmodule]#
    </xar:foreach>
    </xar:if>

    <br />
    <xar:if condition="!empty($maplink) or !empty($archivelink)">
    <table border="0" width="95%">
    <tr>
        <xar:if condition="!empty($maplink)">
        <td align="left">
            <a href="#$maplink#">#$maplabel#</a>
        </td>
        </xar:if>
        <xar:if condition="!empty($archivelink)">
        <td align="right">
            <a href="#$archivelink#">#$archivelabel#</a>
        </td>
        </xar:if>
    </tr>
    </table>
    </xar:if>';
}

function converter_adminapi_addxarcss()
{
    echo '

    /* these defaults apply to the left block group */
    .xar-block-head{
        border-bottom: 1px solid #191970;
        color: #191970;
    }
    .xar-block-title {
        font-weight: bold;
        font-style: italic;
        font-size: 11pt;
    }
    .xar-block-body {}
    .xar-block-foot {
        border-bottom: 2px dotted #dddddd;
        margin-bottom: 8px;
    }

    /* right block group classes */
    .xar-block-head-right{
        border: 1px solid #E47C55;
        background-color: transparent;
    }
    .xar-block-title-right {
        font-weight: bold;
        font-size: 11pt;
    }
    .xar-block-body-right {}
    .xar-block-foot-right {
        border-bottom: 2px solid #cccccc;
        margin-bottom: 8px;
    }

    /* topnav block group classes */
    .xar-block-head-topnav{}
    .xar-block-title-topnav {}
    .xar-block-body-topnav {
        margin: auto;
        border: 1px solid #CCE6FF;
    }
    .xar-block-foot-topnav {}

    /* center block group classes */
    .xar-block-head-center{
        background-image: url(../images/sh_line.gif);
        height: 6px;
        width: 100%;
        background-position: center center;
        background-repeat: repeat-y;
    }
    .xar-block-title-center {
        color: #E47C55;
        background-color: inherit;
        font-weight: bold;
        font-size: 11pt;
        text-align: center;
    }
    .xar-block-body-center {
        background-color: transparent;
    }
    .xar-block-foot-center {
        background-image: url(../images/sh_line.gif);
        height: 3px;
        width: 100%;
        background-position: center center;
        background-repeat: repeat-y;
        margin-bottom: 4px;
    }

    .xar-mod-head {
       border: 2px solid #a0a0a0;
       background-color: transparent;
       padding: 3px;
       text-align: center;
       font-weight: bold;
       font-size: 11pt;
    }
    .xar-mod-title {
       color: #191970;
       font-size: 11pt;
       font-weight: bold;
       font-family: Tahoma,Verdana,Arial,Helvetica,sans-serif;
    }
    .xar-mod-body {}
    .xar-mod-foot {}
    .xar-alt {background-color: #E47C55; }
    .xar-accent {background-color: transparent; }
    .xar-alt-outline {border: 1px solid #E47C55; }
    .xar-accent-outline {border: 1px solid #CCE6FF; }
    .xar-norm-outline {border: 1px solid #c0c0c0; }
    .xar-norm {background-color: transparent; }
    .xar-sub {font-size: smaller;}
    .xar-menu-section {
       color: #191970;
       font-size: 11pt;
       font-weight: bold;
       font-family: Tahoma,Verdana,Arial,Helvetica,sans-serif;
       border-bottom: thin dotted #a0a0a0;
       line-height: 170%;
       width: 100%;
       margin-top: 4px;
       margin-bottom: 4px;
    }
    .xar-menu-section-current {
       background-color: transparent;
       color: #191970;
       font-size: 11pt;
       font-weight: bold;
       font-family: Tahoma,Verdana,Arial,Helvetica,sans-serif;
       border-bottom: 1px solid #191970;
       line-height: 170%;
    }
    .xar-menu-item {
       font-size: 9.5pt;
       font-weight: bold;
       font-family: Tahoma,Verdana,Arial,Helvetica,sans-serif;
       padding-left: .5em;
    }
    .xar-menu-item-current {
       background-color: transparent;
       font-size: 9.5pt;
       font-weight: bold;
       font-family: Tahoma,Verdana,Arial,Helvetica,sans-serif;
       padding-left: .5em;
       padding-bottom: .3em;
    }
    .xar-menu-item:hover {
       background-color: transparent;
    }
    .xar-menu-subitem {
       font-size: 9pt;
       font-weight: bold;
       font-family: Tahoma,Verdana,Arial,Helvetica,sans-serif;
       margin-left: 6px;
       padding-left: 0px;
    }
    .xar-menu-subitem:hover {
       background-color: transparent;
    }
    .xar-menu-subitem-current {
       font-weight: bold;
       color: #000000;
       background-color: transparent;
       font-size: 9pt;
       margin-left: 6px;
       padding-left: 0px;
    }
    .xar-menu-item-current > .xar-menu-subitem > A {
       color: #193970;
    }
    .xar-menu-item-current > .xar-menu-subitem > A:visited {
       color: #003366;
    }

    .xar-error {
       color: #FF0000;
    }';
}

// simulate footer.php
function footmsg()
{
   echo "#xarModGetVar('themes', 'SiteFooter')# "."\n";
   echo "<xar:comment> Body JavaScript </xar:comment>\n".
        "<xar:base-render-javascript position=\"body\" />\n";
}

// Simulate PHPNuke themeheader

function converter_adminapi_themeheadersimulate($theme)
{

    if (file_exists(xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/header.html")) {
        $tmpl_file = xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/header.html";
        $thefile = implode("", file($tmpl_file));
        $thefile = addslashes($thefile);
        $thefile = "\$r_file=\"".$thefile."\";";
        eval($thefile);
        print $r_file;
    }

    echo '<xar:blockgroup name="left" id="left" />';

    if (file_exists(xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/left_center.html")) {
        $tmpl_file = xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/left_center.html";
        $thefile = implode("", file($tmpl_file));
        $thefile = addslashes($thefile);
        $thefile = "\$r_file=\"".$thefile."\";";
        eval($thefile);
        print $r_file;
    }
}

function converter_adminapi_themefootersimulate($theme)
{

    if (file_exists(xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/center_right.html")) {
        $tmpl_file = xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/center_right.html";
        $thefile = implode("", file($tmpl_file));
        $thefile = addslashes($thefile);
        $thefile = "\$r_file=\"".$thefile."\";";
        eval($thefile);
        print $r_file;
    }

    echo '<xar:blockgroup name="right" id="right" />';

    if (file_exists(xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/footer.html")) {
        $tmpl_file = xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/footer.html";
        $thefile = implode("", file($tmpl_file));
        $thefile = addslashes($thefile);
        $thefile = "\$r_file=\"".$thefile."\";";
        eval($thefile);
        print $r_file;
    }
}

function converter_adminapi_themeblocksimulate($theme, $title, $content)
{

    if (file_exists(xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/center_right.html")) {
        $tmpl_file = xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/blocks.html";
        $thefile = implode("", file($tmpl_file));
        $thefile = addslashes($thefile);
        $thefile = "\$r_file=\"".$thefile."\";";
        eval($thefile);
        print $r_file;
    }
}

function themes_get_language($script = 'global')
{
    return;
}

function pnBannerDisplay()
{
    return;
}

function getUserTime()
{
    return;
}

function blocks()
{
    return;
}

function authorised()
{
    return;
}

function ml_ftime()
{
    return;
}

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Converter module development team
 * @return array containing the menulinks for the main menu items.
 */
function converter_adminapi_getmenulinks()
{

// Security Check
    if (xarSecurityCheck('Adminconverter',0)) {

        $menulinks[] = Array('url'   => xarModURL('converter',
                                                   'admin',
                                                   'pntheme'),
                              'title' => xarML('Convert a PostNuke Theme Automatically'),
                              'label' => xarML('PostNuke Theme'));
        $menulinks[] = Array('url'   => xarModURL('converter',
                                                   'admin',
                                                   'phpnuketheme'),
                              'title' => xarML('Convert a PHPNuke Theme Automatically'),
                              'label' => xarML('PHPNuke Theme'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
