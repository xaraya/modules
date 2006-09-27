<?php

function teamsort($a, $b) 
{
    if ($a['membername'] == $b['membername']) {
        return 0;
    }
    return ($a['membername'] < $b['membername']) ? -1 : 1;
}


?>