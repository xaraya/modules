<?php

function bkview_user_graphproducer($args)
{
    static $graph = null; 
    
    if(!xarVarFetch('repoid','id',$repoid)) return;
    if(!xarVarFetch('start','str::',$start,'-3d',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('end','str::',$end,'+', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('file','str::', $file, 'ChangeSet', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('format','str::',$format,'png', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('spc','checkbox:',$spc,true,XARVAR_NOT_REQUIRED)) return;
    extract($args);
    
    if(is_null($graph)) 
    {
        // For the specified range, get the lines
        $item = xarModAPIFunc('bkview','user','get', array('repoid' => $repoid));
        if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
        
        $repo =& $item['repo'];
        //xarLogMessage("BK: passing start=$start,end=$end,file=$file");
        $graphdata =& $repo->bkGetLines($start, $end, $file);
        if($format =='debug') {
            echo "<pre>".var_export($graphdata,true)."</pre>"; 
            die();
        }
        include_once "modules/bkview/xarincludes/GraphViz.php";
        
        $graph = new Image_GraphViz();
        

        foreach($graphdata['nodes'] as $node)
        {
            $attributes = array('href' => xarModUrl('bkview','user','deltaview', array('repoid' => $repoid, 'rev' => $node)),
                                'tooltip' => xarML('Show details for revision #(1)',$node),'label' => $node);
            if($node == $graphdata['startRev'] || $node == $graphdata['endRev']) $attributes['color'] ='red';
            if(!in_array($node, $graphdata['pastconnectors']))
            {
                // Normal node
                $attributes['shape'] = 'box';
                $graph->addNode($node, $attributes);
            } elseif($spc) {
                // Past connector node
                $attributes['shape'] = 'ellipse';
                $attributes['style'] = 'dashed';
                $graph->addNode($node, $attributes);
            }
        }
        foreach($graphdata['edges'] as $edge) 
        {
            if(!in_array(key($edge),$graphdata['pastconnectors']) || $spc)
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