<?php

/**
 * File: $Id$
 *
 * Create a new page.
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

/*
 * This is a completely self-contained dynamic CSS script.
 * It does not refer to or load the core in any way.
 * Consequently care must be taken that parameters are properly
 * validated and that security holes are plugged.
 *
 * This is not necessarily the 'standard' way to handle dynamic
 * CSS in Xaraya, but is just the way I have chosen to handle it
 * in order to publish this code. Without it, publishing would be
 * much more cumbersome.
 *
 * If a minimal core were loaded, it would then be possible to
 * rewrite image URLs etc to pick up images from the current
 * theme when available, allowing theme-overrideable images 
 * referenced in a module CSS script.
 *
 * Now the really clever bit, that I would like to implement,
 * would be to make this script generic, and drive it from a
 * real CSS script. The CSS script could contain commands embedded
 * in comments, allowing the CSS to be rewritten on-the-fly and
 * streamed out. The big advantage of doing that, is that there
 * would always be a valid CSS script available for development
 * in a non-PHP environment.
 *
 * An example of how this could work would be (note: read * / as a closing comment):
 *
 * div.my_container li { /*xar_replace: div.${container_class} li {* /
 *  margin: 0; /*xar_replace: margin: ${margin_size};* /
 * }
 *
 * In the above example, the comments could indicate that the complete
 * line should be replaced by the expression in the comments. A browser
 * would normally interpret this as simple comments. Other comments
 * could enable or disable sections depending upon browser sniffing, or
 * even session or user vaiables, or parameters passed in (all sorts could
 * be referenced). It could become a very complex meta-language, so before
 * embarking down that route, it would be worth investigating ready-built
 * solutions.
 *
 * It may accept parameters and it may check the browser version.
 */

function set_header()
{
    header("Content-type: text/css");
    header("Cache-Control: must-revalidate");
    // We always want to revalidate, since the same script could be called
    // up many times in one page, with different parameters each time.
    $offset = 1; //60 * 60 ;
    $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
    header($ExpStr);
}

function sniffer()
{
    $http_agent = getenv('HTTP_USER_AGENT');

    if (ereg('MSIE ([0-9].[0-9]{1,2})', $http_agent, $ver)) {
        $version = $ver[1];
        $agent = 'MSIE';
    } elseif (ereg('Opera[ /]([0-9].[0-9]{1,2})', $http_agent, $ver)) {
        $version = $ver[1];
        $agent = 'Opera';
    } elseif (ereg('Konqueror/([0-9].[0-9]{1,2})', $http_agent, $ver)) {
        $version = $ver[1];
        $agent = 'Konqueror';
    } elseif (ereg('Netscape6/([0-9].[0-9]{1,2})', $http_agent, $ver)) {
        $version = '6';
        $agent = 'Netscape';
    } elseif (ereg('Netscape/([0-9].[0-9]{1,2})', $http_agent, $ver)) {
        $version = $ver[1];
        $agent = 'Netscape';
    } elseif (ereg('Firefox/([0-9]{1,2})', $http_agent, $ver)) {
        $agent = 'Mozilla Firefox';
        $version = $ver[1];
    } elseif (ereg('Mozilla/([0-9].[0-9]{1,2})', $http_agent, $ver)) {
        $version = $ver[1];
        $agent = 'Mozilla';
    } elseif (ereg('Safari/([0-9]{1,2})', $http_agent, $ver)) {
        $agent = 'Safari';
        $version = $ver[1];
    } else {
        $version = 0;
        $agent = 'Other';
    }

    $version_parts = preg_split('/[.]/', $version);

    $platform = 'Other';
    $platforms = array('Win', 'Mac', 'Linux');
    foreach($platforms as $check) {
        if (strstr($http_agent, $check)) {
            $platform = $check;
            break;
        }
    }

    return array(
        'agent' => $agent,
        'version' => $version,
        'version_parts' => $version_parts,
        'platform' => $platform
    );
}

/* Start */
set_header();
$sniff = sniffer();

// Do some calculations. All widths are in pixels.
// We could really go to town on this, and make every dimension
// a configurable value that cascades down to its descendant
// elements. This is enough for now though.
$total_width = 170; // Width of menu container.
$anchor_border = 1;

// We name the container class here, for ease of customisation.
// We could also set a container ID. Either way, it could allow this
// stylesheet to be invoked several times for one page, delivering
// slightly different styles to individual named blocks.
$container_class = 'xarpages-side-menu1';

$top_width = $total_width - 4 - 2; // padding=2; container border=1

$anchor1_width = $top_width - 4 - 20 - 2*$anchor_border; // padding=4 and 20; border=1
$anchor2_width = $top_width - 20 - 20 - 2*$anchor_border; // padding=20 and 20; border=1
$anchor3_width = $top_width - 30 - 6 - 2*$anchor_border; // padding=30 and 6; border=1
$anchor4_width = $top_width - 40 - 6 - 2*$anchor_border; // padding=30 and 6; border=1

// Send some IE5.X specific styles
if ($sniff['agent'] == 'MSIE' && $sniff['version_parts'][0] == '5') {
    $top_width = $total_width - 2; // Not sure why; I would not expect the '-2'.
    // All anchors the same.
    $anchor1_width = $total_width - 8;
    $anchor2_width = $anchor1_width;
    $anchor3_width = $anchor1_width;
    $anchor4_width = $anchor1_width;
}

// MSIE "whitespace bug" for all known versions of IE.
if ($sniff['agent'] == 'MSIE' && ($sniff['version_parts'][0] == '5' || $sniff['version_parts'][0] == '6')) {
    $msie_whitespace_fix = 'display: inline; /* IE5 and IE6 whitespace bug workaround */';
} else {
    $msie_whitespace_fix = '';
}

// Determine where the images are: either ../images or ../xarimages
// We will assume (for now) all the images are local to this script,
// which will be either in the module xarscripts or the theme scripts
// directory.
if (is_dir(realpath(dirname(__FILE__) . '/../xarimages'))) {
    $image_path = '../xarimages';
} elseif (is_dir(realpath(dirname(__FILE__) . '/../images'))) {
    $image_path = '../images';
} else {
    $image_path = '.';
}


/* Send the main CSS */
echo <<< ENDCSS

/* Auto-generated for "$sniff[agent]" version "$sniff[version]" */

/* Reset some of the defaults that the theme adds. */
div.${container_class} li, div.${container_class} ul {
   margin: 0;
   padding: 0;
   text-indent: 0;
}
div.${container_class} ul, div.${container_class} li {
    line-height: 120%;
}
div.${container_class} ul {
    list-style-position: default;
}


/***************************
 * Layout styles 
 */

div.${container_class} {
   width: ${top_width}; /* 174px */
   padding: 2px;
   border-width: 1px;
   /* Style is formatting really, but there is no width without a style */
   border-style: solid;
}

/* All anchors are 174px wide external, with a 1px border, and pixel of 
 vertical (and collapsable) margin.
 Internal padding and width varies with the level, but always adds up to 172px
 */
div.${container_class} a, div.${container_class} h2 {
   display: block;
   padding: 2px 0;
   margin: 0px 0px;
   width: ${top_width}px; /* 172px Will be overridden in all cases */
   border-width: ${anchor_border}px;
   border-style: solid;
}


/* Remove default styles for lists */
div.${container_class} ul {
   list-style-type: none;
}

/* Level 1 anchors: 20px to LHS */
div.${container_class} ul.side-menu1 a, div.${container_class} ul.side-menu1 h2 {
   width: ${anchor1_width}px; /* 148px */
   padding-right: 4px;
   padding-left: 20px;
}

/* Level 2: same indent as level 1, more padding to RHS */
div.${container_class} ul.side-menu2 a, div.${container_class} ul.side-menu2 h2 {
   width: ${anchor2_width}px; /* 132px */
   padding-right: 20px;
   padding-left: 20px;
}

/* Level 3: 10px more padding on left than level 2 */
div.${container_class} ul.side-menu3 a, div.${container_class} ul.side-menu3 h2 {
   width: ${anchor3_width}px; /* 136px */
   padding-left: 30px;
   padding-right: 6px;
}

/* Level 4: 8px more padding on left than level 3 */
div.${container_class} ul.side-menu4 a, div.${container_class} ul.side-menu4 h2 {
   width: ${anchor4_width}px; /* 126px */
   padding-left: 40px;
   padding-right: 6px;
}



/***************************
 * Static formatting styles
 * This do not affect the sizes
 * or positions of any of the
 * elements.
 * The positioning will, howver,
 * affect the placing of the
 * background image bullets.
 * Ideally this section should be
 * in a different file so the colours
 * can be overridden without affecting
 * other styles.
 */

div.${container_class} {
   font-family: Verdana, Arial, Helvetica, sans-serif;
   font-size: 80%;
   border-color: #00FFFF;
   border-style: solid;
   background-color: #00FFFF;
}

div.${container_class} h2 {
   font-size: 98%;
}
   
div.${container_class} li {
   background-repeat: no-repeat;
    ${msie_whitespace_fix}
}

/* Default font style and colours for the menu links */
div.${container_class} a, div.${container_class} h2 {
   text-align: left;
   text-decoration: none;
   font-weight: bold;
   color: #666;
   border-color: #00ffff;
}

/* A blue tinge to the links after the first level */
div.${container_class} ul.side-menu2 a, div.${container_class} ul.side-menu2 h2 {
   color: #669;
}
/* Disable bold after the second level */
div.${container_class} ul.side-menu3 a {
   font-weight: normal;
}

/* Menu background colours - lighter blue for deeper levels */
div.${container_class} ul.side-menu1 li {
   background-color: #00FFFF;
}
div.${container_class} ul.side-menu1 li a {
    border-color: #00FFFF;
}
div.${container_class} ul.side-menu2 li {
   background-color: #AAFFFF;
}
div.${container_class} ul.side-menu2 li a {
    border-color: #AAFFFF;
}
div.${container_class} ul.side-menu3 li {
   background-color: #CCFFFF;
}
div.${container_class} ul.side-menu3 li a {
   border-color: #CCFFFF;
}
/* Selected menu item: background colour white - font and border set on the anchor */
div.${container_class} ul.side-menu1 li.selected,
div.${container_class} ul.side-menu2 li.selected,
div.${container_class} ul.side-menu3 li.selected,
div.${container_class} ul.side-menu4 li.selected {
   background-color: #ffffff;
}

/* Current page has a highlighted border and bolder text */
div.${container_class} li a.selected {
   border-color: #333333;
   border-style: dotted;
   color: #600;
   /*font-style: italic;*/
   font-weight: bold;
}

/* Bullets for level 1:
 [] = no children; blue = not selected; red = selected
 >  = closed, has children; blue
 v  = open, has children; blue = not selected; red = selected
*/

/* Default bullet - assuming no children. */
div.${container_class} ul.side-menu1 li {
   background-position: 0px 0.25em;
   background-image: url(${image_path}/bull1.gif);
}
/* Closed, with children */
div.${container_class} ul.side-menu1 li.closed {
   background-position: 0px 0.25em;
   background-image: url(${image_path}/bull1-closed.gif);
}
/* Open - not selected */
div.${container_class} ul.side-menu1 li.open {
   background-position: 0px 0.25em;
   background-image: url(${image_path}/bull1-open.gif);
}

/* Level 2 - no bullets (try some small bullets) */
div.${container_class} ul.side-menu1 ul.side-menu2 li {
   /*background-image: none;*/
   background-position: 4px 0.5em;
   background-image: url(${image_path}/bull4.gif);
}

/* Level 3 - simple square bullets */
div.${container_class} ul.side-menu2 ul.side-menu3 li {
   background-position: 20px 0.5em;
   background-image: url(${image_path}/bull3.gif);
}

/* Level 4 - simple round bullets */
div.${container_class} ul.side-menu3 ul.side-menu4 li {
   background-position: 30px 0.5em;
   background-image: url(${image_path}/bull4.gif);
}


/***************************
 * Dynamic formatting styles
 */

/* Darken the text and border when hovering */
/* Need to include ul.side-menu1 for specificity */
div.${container_class} ul.side-menu1 li a:hover {
   border-color: #600;
   color: #600;
   text-decoration: none;
}

/* MSIE MAC may need all anchors floated to the left - we will treat that as dead browser for now */
ENDCSS;

?>