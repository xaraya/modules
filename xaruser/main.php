<?php
/**
 * Xaraya BBCode
 *
 * Based on pnBBCode Hook from larsneo 
 * http://www.pncommunity.de
 * Converted to Xaraya by John Cox
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BBCode Module
 * @link http://xaraya.com/index.php/release/778.html
 * @author John Cox
*/
/**
 * Test BB Code Output
 *
 * @param $args['text'] is the bbcode text to transform
 * @returns array
 * 
 */
function bbcode_user_main()
{
    $text = array();
    $data = array();
    if(!xarVarFetch('text', 'str', $text['text'], '' , XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    // Security Check

    if (isset($text)){
    // Do transform
        $text = xarModCallHooks('item', 'transform', 1, $text, 'bbcode');
        $data['output'] = $text[0];
        $data['output'] = var_dump($data['output']);
    }

    $data['submit'] = xarML('Submit');    
    return $data;
}
?>