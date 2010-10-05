<?php
function fulltext_xartables()
{
    $prefix = xarDB::getPrefix();
    $tables = array(
        'fulltext' => $prefix . "_fulltext",
    );
    return $tables;
}
?>