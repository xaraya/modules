<?php 
/**
 * File: $Id: s.xaruser.php 1.45 03/01/17 18:39:10+01:00 jan@jack.iwr.uni-heidelberg.de $
 *
 * Figlet
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage Figlet
 * @author Lucas Baltes, John Cox 
 *
*/

/**
 * The standard figlet output
 *
 * @param $args['font'] is the figlet font
 * @param $args['text'] is the figlet text to transform
 * @returns array
 * 
 */
function figlet_user_main()
{
    list($font,
         $text) = xarVarCleanFromInput('font',
                                       'text');

    // Security Check
	if(!xarSecurityCheck('ReadFiglet')) return;

    if (!empty($font)) {
        $font = xarVarPrepForDisplay($font);
    } else {
        $font = '';
    }
    if (!empty($text)) {
        $text = xarVarPrepForDisplay($text);
    } else {
        $text = '';
    }

    if (!empty($font)){
        require("modules/figlet/xarclass/phpfiglet_class.php");

        $phpFiglet = new phpFiglet();

        if ($phpFiglet->loadFont("modules/figlet/xarfonts/$font")) {
            $data['output'] = $phpFiglet->display("$text");
        } else {
            $msg = xarML('There is no font defined.');
            xarExceptionSet(XAR_USER_EXCEPTION, 'NoFont', new DefaultUserException($msg));
            return;
        }
    }

    if (empty($data['output'])){
        $data['message'] = xarML('Choose font and a text to be transformed');
    } 

    if (empty($data['message'])){
        $data['message'] = '';
    }

    // Get Fonts

    $handle = opendir('modules/figlet/xarfonts');

    while($f = readdir($handle)){
        if ($f != '.' && $f != '..' && $f != 'SCCS'){
            $fontdir[] = $f;
        }
    }

    closedir($handle);
    sort ($fontdir);
    $data['fontselect'] = array();

    foreach ($fontdir as $v){
        $data['fontselect'][] = array('fontname' => $v);
    }

    $data['submit'] = xarML('submit');
    $data['font'] = $font;
    $data['text'] = $text;
    
    return $data;
}

?>