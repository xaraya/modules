<?php
function helpdesk_userapi_buildswdrops($args)
{
    extract($args);
    $allowversionchoice  = xarModGetVar('helpdesk', 'AllowVersionChoice');
    $software_data       = xarModAPIFunc('helpdesk','user','getsoftware', 
                                         array('select' => true));

    // First build the Software Drop box:
    // Get Array from api
    $options = '';
    $DynScriptSupp = '';
    $iA=0;
    foreach ($software_data as $softwareitem){
        if($softwareid == $softwareitem['software_id']){
            $itemselected="selected=\"selected\"";
        }else{
            $itemselected='';
        }
        // Each pass through this outer loop needs to reset $i2 to zero and increment $iA
        $i2=0;
        $iA++;
        $options .= "<option value=\"$softwareitem[software_id]\"$itemselected>$softwareitem[software_name]</option>\n";
        if ($allowversionchoice){
            // If we'll be doing versions, let's add corresponding software versions here!
            $swversion_data = xarModAPIFunc('helpdesk','user','getswversions',
                                            array('selected_id'=>$softwareitem['software_id']));
                                            
            $DynScriptSupp .= "group[$iA][$i2]=new Option('----------------','0')\n";
            if (count($swversion_data) > 0){
                foreach ($swversion_data as $swversionitem){
                    $i2++;
                    $DynScriptSupp .= "group[$iA][$i2] = new Option(\"$swversionitem[name]\", \"$swversionitem[id]\")\n";
                } // End swversion_data loop
            } // End If swversion count
        } // End allowversion choice
    } // End software_data loop    
    // End the Loop, close the Select box
    $data['options']             = $options;
    $data['allowversionchoice']  = $allowversionchoice;    
    $data['swversionid']         = $swversionid;    
    $data['softwareid']          = $softwareid;
    $data['DynScriptSupp']       = $DynScriptSupp;
    
    if ($allowversionchoice){
        if($swversionid){
            $temp = xarModAPIFunc('helpdesk','user','get',
                                  array('object' => 'swversion',
                                        'itemid' => $swversionid
                                       )
                                 );
            
            $data['swversionname'] = $temp;
        }
        $Drop1Count = count($software_data)+1;
        // Now place the Javascript for the second box to react to the first box changes
        // Finally, output the javascript code into the $output object
        
        $DynScript = "<script Language='JavaScript'>\n";
        $DynScript .= "<!--\n";
        $DynScript .= "var groups=".$Drop1Count."\n";
        $DynScript .= "var group=new Array(groups)\n";
        $DynScript .= "for (i=0; i<groups; i++)\n";
        $DynScript .= "group[i]=new Array()\n";
        // This first entry corresponds to the dashed entry in the first drop box
        $DynScript .= "group[0][0]=new Option('----------------','0')\n";
        // Cycle through and output javascript code from the versions table
        // format:
        // group[index of software drop][item index]=new Option('name','value')newline
        // Grab the $DynScriptSupp created earlier and insert at this point
        $DynScript .= $DynScriptSupp;

        // Finish up the javascript code
        $DynScript .= "var temp=document.".$formname.".swv_id\n";					
        $DynScript .= "function dropchg(x){\n";
        $DynScript .= "for (m=temp.length-1;m>0;m--)\n";
        $DynScript .= "temp.options[m]=null\n";
        $DynScript .= "for (i=0;i<group[x].length;i++){\n";
        $DynScript .= "temp.options[i]=new Option(group[x][i].text,group[x][i].value)\n";
        $DynScript .= "}\n";
        $DynScript .= "temp.options[0].selected=true\n";
        $DynScript .= "}\n";
        $DynScript .= "//-->\n";
        $DynScript .= "</script>\n";
        
        $data['dynscript'] = $DynScript;
    }
    return xarTplModule('helpdesk', 'user', 'buildswdrops', $data);
}
?>
