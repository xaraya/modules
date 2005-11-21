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
    if(!xarVarFetch('branch','str::',$branch,'',XARVAR_NOT_REQUIRED)) return;
    extract($args);
    
    if(is_null($graph)) 
    {
        // For the specified range, get the lines
        $item = xarModAPIFunc('bkview','user','get', array('repoid' => $repoid));
        if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
        
        $repo =& $item['repo'];
        xarLogMessage("MT: passing start=$start,end=$end,file=$file,branch=$branch");
        $graphdata =& $repo->GetGraphData($start, $end, $file,$branch);
        if($format =='debug') {
            echo "<pre>".var_export($graphdata,true)."</pre>"; 
            die();
        }
        include_once "modules/bkview/xarincludes/GraphViz.php";
        
        $graphattrs = array('ratio'=>'compress');
        $graph = new Image_GraphViz(true,$graphattrs);
        
        // Common attributes
        // TODO: make the dot file a BL job (i.e. template it)
        $nodeattrs = array('spline'=>true,'fontname'=>'Windsor','fontsize'=>'8.0','nodesep'=>'0.1','ranksep'=>'0.2','height'=>'0.3','shape'=>'box');
        foreach($graphdata['nodes'] as $node)
        {
            $nodeattrs['href']    = xarModUrl('bkview','user','deltaview', array('repoid'=>$repoid,'rev'=>$node['rev'],'branch'=>$branch));
            $nodeattrs['tooltip'] = xarML('Show details for revision #(1) by #(2)',$node['rev'],$node['author']);
            $nodeattrs['label']   = substr($node['rev'],0,8).'... on '.substr($node['date'],0,10).'\n'.$node['author'].'\n'.$node['tags'];
            
            if($node['rev'] == $graphdata['startRev'] || $node['rev'] == $graphdata['endRev']) $nodeattrs['color'] ='red';
            $nodeattrs['fillcolor'] = colour_from_string($node['author']);
            $nodeattrs['style'] ='filled';

            if(!in_array($node['rev'], $graphdata['pastconnectors']))
            {
                // Normal node
                $graph->addNode($node['rev'], $nodeattrs);
            } elseif($spc) {
                // Past connector node
                $graph->addNode($node['rev'], $nodeattrs);
            }
        }
        $edgeattrs = array('fontsize'=>'8.0','fontname'=>'Windsor','dir'=>'back','label'=>'diff','fontcolor'=>'cornflowerblue');
        foreach($graphdata['edges'] as $edge) 
        {
            if(!in_array(key($edge),$graphdata['pastconnectors']) || $spc) {
                $params = array('repoid'=>$repoid,'branch'=>$branch);
                $edgeattrs['href'] = xarModUrl('bkview','user','patchview',$params);
                $graph->addEdge($edge,$edgeattrs);
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