<?php

function bkview_user_graphproducer($args)
{
    static $graph = null; 
    if(!xarVarFetch('repoid','id',$repoid)) return;
    if(!xarVarFetch('start','str::',$start,'-3d',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('end','str::',$end,'+', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('file','str::', $file, 'ChangeSet', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('format','str::',$format,'png', XARVAR_NOT_REQUIRED)) return;
    extract($args);
    
    if(is_null($graph)) 
    {
        // For the specified range, get the lines
        $item = xarModAPIFunc('bkview','user','get', array('repoid' => $repoid));
        if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
        
        $repo =& $item['repo'];
        //xarLogMessage("BK: passing start=$start,end=$end,file=$file");
        $graphdata =& $repo->bkGetLines($start, $end, $file);
        //echo "<pre>".var_export($graphdata,true)."</pre>"; die();
        include_once "modules/bkview/xarincludes/GraphViz.php";
        
        $graph = new Image_GraphViz();
        // Set some default attributes
        $attributes = array (
                             'fontsize' => 8.0,
                             'labelfontsize' => 8.0
                             );
        $graph->setAttributes($attributes);
        foreach($graphdata['nodes'] as $node)
        {
            // Don't include the connector nodes to the past for now
            // TODO: see if this is a usefull option to switch on and off (include dashed line to it?)
            if(!in_array($node, $graphdata['pastconnectors']))
            {
                $graph->addNode($node, array(
                                             //http://xartest.hsdev.com/index.php?module=bkview&func=deltaview&repoid=3&rev=1.2050
                                             'URL' => xarModUrl('bkview','user','deltaview', array('repoid' => $repoid, 'rev' => $node)), 
                                             'label'=> $node, 'labelfontsize' => 6.0, 'labelfontname' => 'Helvetica',
                                             'shape' => 'box'));
            }
        }
        foreach($graphdata['edges'] as $edge) 
        {
            if(!in_array(key($edge),$graphdata['pastconnectors']))
            {
                $graph->addEdge($edge,array('fontsize' => 9.0, 'fontname' => 'Helvetica'));
            }
        }
    }
    // This returns a content header plus content, so we exit directly
    switch($format) {
        case 'cmapx';
            return $graph->image($format);
            break;
        default:
            $graph->image($format);
            exit();
    }
}

?>