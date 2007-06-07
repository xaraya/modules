<?php

/**
 * Get reflection items
 *
 * @param itemtype - what do we want to get (funcs, classs, extensions)
 * @param modscope - only things from this module
 * @returns array with items 
 */
sys::import('modules.autodoc.xarclass.reflection');
function autodoc_userapi_get($args)
{
    extract($args);

    if(!isset($itemtype)) $itemtype = ReflectionInfo::FNC; // Default functions
    if(!isset($scope)) $scope = 0; // Default all
 
    // Get the requested items in the current scope
    $items = &items_current_scope($itemtype,$scope);

    return $items;
}

// Helper function to transfer an array id=>name into array('id' => id, 'name' => name)
function ad_NotInternal($value)
{
    $cr = new ReflectionClass($value);
    return $cr->isUserDefined();
}

function ad_Internal($value)
{
    $cr = new ReflectionClass($value);
    return !$cr->isUserDefined();
}

function &items_current_scope($itemtype,$scope = 0)
{
    switch($itemtype) {
        case ReflectionInfo::FNC: // Functions
            $items = get_defined_functions();
            switch($scope) {
                case 0: // all
                    $items = array_merge($items['user'],$items['internal']);
                    break;
                case 1: // internal
                    $items = $items['internal'];
                    break;
                case 2: // user defined
                    $items = $items['user'];
            }
            break;
        case ReflectionInfo::CLS: // Classes
            $items = get_declared_classes(); // This gets them all
            sort($items);
            switch($scope) {
                case 1: // internal
                    $items = array_filter($items,'ad_Internal');
                    break;
                case 2: // user defined
                    $items = array_filter($items,'ad_NotInternal');
                    break;
            }
            break;
         case ReflectionInfo::EXT: // Extensions
             $items = get_loaded_extensions();
             break;
         case ReflectionInfo::CON: // Constants
             $items = get_defined_constants(); // Gets them all, uncategorized
             $items = array_keys($items);      // Lose the values for now, just the names
             switch($scope) {
                 case 1: // internal
                     $useritems = get_defined_constants(true);
                     $useritems = $useritems['user'];
                     $useritems = array_keys($useritems);
                     $items = array_diff($items,$useritems);
                     break;                     
                 case 2: // user defined
                     $items = get_defined_constants(true);
                     $items = $items['user'];
                     $items = array_keys($items);
                     break;
             }
             break;
         case ReflectionInfo::INT: // Interfaces
             $items = get_declared_interfaces();
             break;
    }
    sort($items);
    $offsetted = array();
    foreach($items as $id => $value) {
        // unique = itemtype + name
        $key = ReflectionInfo::GetID($value,$itemtype);
        $offsetted[$key] = $value;
    }
    return $offsetted;
}
?>