<?php 
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

    if (!empty($text)){
    // Do transform
        $text = xarModCallHooks('item', 'transform', 1, $text, 'bbcode');
        $data['output'] = $text[0];
        //$data['output'] = var_dump($data['output']);
    }

    $data['submit'] = xarML('Submit');    
    return $data;
}
?>