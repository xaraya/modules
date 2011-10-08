<?php 
/**
 * Figlet Module
 *
 * @package modules
 * @subpackage figlet module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Lucas Baltes, John Cox
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
    if (!xarVarFetch('font', 'str:1:100', $font, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('text', 'str:1:100', $text, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

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
        sys::import('modules.figlet.xarclass.phpfiglet_class');

        $phpFiglet = new phpFiglet();

        if ($phpFiglet->loadFont(sys::code()."modules/figlet/xarfonts/$font")) {
            $data['output'] = $phpFiglet->display("$text");
        } else {
            $msg = xarML('There is no font defined.');
            xarErrorSet(XAR_USER_EXCEPTION, 'NoFont', new DefaultUserException($msg));
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

    $handle = opendir(sys::code().'modules/figlet/xarfonts');

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

    $data['font'] = $font;
    $data['text'] = $text;
    
    return $data;
}

?>