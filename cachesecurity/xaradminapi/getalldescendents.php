<?php

/**
 * Generate an array with all the descendents of all ids 
 */
function cachesecurity_adminapi_getalldescendents($args)
{
    //$array, $id, $id_column, $parent_column $depth
    extract($args);

    if (!isset($depth)) $depth = 1;
    
    $links = array();
    $size = count($array);
    for ($i=0;$i<$size;$i++) {
        //Is this a son?
        if ($array[$i][$parent_column] == $id) {
            $links[$array[$i][$id_column]] = $depth;

            $new_links = cachesecurity_adminapi_getalldescendents(array(
                'array' => $array, 'id' => $array[$i][$id_column],
                'id_column' => $id_column, 'parent_column' => $parent_column,
                'depth' => $depth +1
            )); 

            foreach ($new_links as $key => $value) {
                //Set with the new link only if there isnt a link to that key yet
                //OR if the distance that was there before was bigger 
                if (!isset($links[$key]) || $links[$key] > $value) {
                    $links[$key] = $value;
                }
            }
        }
    }

    return $links;
}

?>