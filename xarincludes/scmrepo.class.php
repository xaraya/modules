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
}

?>