<?php

sys::import('modules.dynamicdata.class.objects.base');

class ArticlePubTypeObject extends DataObject
{
    public $visibility = 'protected';
}

sys::import('modules.dynamicdata.class.objects.list');

class ArticlePubTypeObjectList extends DataObjectList
{
    public $visibility = 'protected';
}
?>
