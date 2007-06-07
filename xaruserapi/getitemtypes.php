<?php

sys::import('modules.autodoc.xarclass.reflection');
function autodoc_userapi_getitemtypes($args)
{
    $itemtypes = array();
    
    $itemtypes[ReflectionInfo::FNC] = array('id'    =>ReflectionInfo::FNC,
                          'name' => xarML('Functions'), 
                          'label' => xarML('Functions'), 
                          'title' => xarML('Functions'),
                          'url'   => xarModUrl('autodoc','user','view',array('itemtype' => ReflectionInfo::FNC)));
    $itemtypes[ReflectionInfo::CLS] = array('id'    => ReflectionInfo::CLS,
                          'name' => xarML('Classes'), 
                          'label' => xarML('Classes'), 
                          'title' => xarML('Classes'),
                          'url'   => xarModUrl('autodoc','user','view',array('itemtype' => ReflectionInfo::CLS)));
    $itemtypes[ReflectionInfo::EXT] = array('id'    => ReflectionInfo::EXT,
                          'name' => xarML('Extensions'), 
                          'label' => xarML('Extensions'), 
                          'title' => xarML('Extensions'),
                          'url'   => xarModUrl('autodoc','user','view',array('itemtype' => ReflectionInfo::EXT)));
    $itemtypes[ReflectionInfo::CON] = array('id'    => ReflectionInfo::CON,
                          'name' => xarML('Constants'), 
                          'label' => xarML('Constants'), 
                          'title' => xarML('Constants'),
                          'url'   => xarModUrl('autodoc','user','view',array('itemtype' => ReflectionInfo::CON)));
    $itemtypes[ReflectionInfo::INT] = array('id'    => ReflectionInfo::INT,
                          'name' => xarML('Interfaces'), 
                          'label' => xarML('Interfaces'), 
                          'title' => xarML('Interfaces'),
                          'url'   => xarModUrl('autodoc','user','view',array('itemtype' => ReflectionInfo::FNC)));

    return $itemtypes;
}
?>