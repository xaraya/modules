<?php


function images_userapi_gd_info() 
{

    $gd_info = array(
        'GD Version'          => 'not supported', 
        'FreeType Support'    => FALSE,
        'T1Lib Support'       => FALSE,
        'GIF Read Support'    => FALSE,
        'GIF Create Support'  => FALSE,
        'JPG Support'         => FALSE,
        'PNG Support'         => FALSE,
        'WBMP Support'        => FALSE,
        'XBM Support'         => FALSE,
        'typesBitmask'        => imagetypes());
    
    ob_start();
    phpinfo(INFO_MODULES);
    $string = ob_get_contents();
    ob_end_clean();

    $pieces = explode('<h2>', $string);
    foreach ($pieces as $key => $piece) {

        if (!stristr($piece, 'module_gd')) {
            unset($pieces[$key]);
        } else {
            $gd_pre = $piece;
            unset($pieces);
            break;
        }            
    }

    if (isset($gd_pre)) {
        $gd_multi = explode("\n", $gd_pre);

        foreach($gd_multi as $key => $line) {
            // skip the first & second key key cuz they're just garbage
            if ($key <= 1)  {
                continue; 
            }
            
            eregi('\<tr\>\<td class="e"\>([^<]*)\<\/td\>\<td class="v"\>([^<]*)\<\/td\>\<\/tr\>', $line, $matches);

            $key   = trim($matches[1]);
            $value = trim($matches[2]);

            switch($value) {
                case 'enabled': 
                    $value = TRUE;
                    break;
                case 'disabled':
                    $value = FALSE;
                    break;
            }
            $gd_info[$key] = $value;
        }
    }

    return $gd_info;
}


?>
