<?php

sys::import('modules.dynamicdata.class.objects.base');

class ArticleObject extends DataObject
{
}

sys::import('modules.dynamicdata.class.objects.list');

class ArticleObjectList extends DataObjectList
{
    function archive()
    {
        echo "hello archive";
    }

    function viewmap()
    {
        echo "hello viewmap";
    }
}

?>
