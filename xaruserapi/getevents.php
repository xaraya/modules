<?php

    sys::import("modules.xen.xarclasses.xenquery");

    function calendar_userapi_getevents($args)
    {
        extract($args);
        $xartable =& xarDBGetTables();

        $q = new xenQuery('SELECT');
        $q->addtable($xartable['calendar_event']);
        $q->ge('start',$day->thisDay(TRUE));
        $q->lt('start',$day->nextDay(TRUE));

        if (!$q->run()) return;
        return $q->output();
    }

?>
