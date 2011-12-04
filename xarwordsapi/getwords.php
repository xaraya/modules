<?php
function keywords_wordsapi_getwords(Array $args=array())
{
    $items = xarMod::apiFunc('keywords', 'words', 'getitems', $args);
    if (empty($items)) return $items;
    foreach ($items as $item)
        $words[$item['keyword']] = $item['keyword'];
    return $words;
}
?>