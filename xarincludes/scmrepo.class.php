<?php

class scmRepo
{
    /**
     * Construct a repository object 
     *
     */
    function construct($brand ='bk', $args)
    {
        include_once "modules/bkview/xarincludes/$brand/$brand.class.php";
        $className =  "$brand"."Repo";
        return new $className($args);
    }
    
    function map($id)
    {
        if(!isset($id)) return;
        switch($id) {
            case 1: return 'bk';
            case 2: return 'mt';
            default: return;
        }
    }
}

?>