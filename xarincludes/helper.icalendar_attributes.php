<?php

function find_attributes(&$assign_to,&$vproperty,$n=-1) 
{
    // ok, let's tidy up and assign some other vars if available:
    if($n >= 0) {
        $property =& $vproperty->{$assign_to}[$n];
    } else {
        $property =& $vproperty->{$assign_to};
    }
    
    
    
    if(preg_match_all('/([A-Z-]+)="(.*)"[:|;]/s',$property,$matches,PREG_SET_ORDER)) {
        //print_r($matches);
        for($i=0,$max=count($matches); $i<$max; $i++) {
            $attribute = $assign_to.'_'.$matches[$i][1];
            $attribute = strtolower($attribute);
            $attribute = str_replace('-','_',$attribute);
            if($n >= 0) {
                $vproperty->{$attribute}[$n] = $matches[$i][2];
            } else {
                $vproperty->{$attribute} = $matches[$i][2];
            }
            // remove the attribute from the string
            if($n >= 0) {
                $vproperty->{$assign_to}[$n] = str_replace($matches[$i][0],'',$property);
            } else {
                $vproperty->{$assign_to} = str_replace($matches[$i][0],'',$property);
            }
            unset($attribute);
        }
    }
    
    // only catch attributes that come before the first colon (:)
    // anything after the first : should be part of the value
    if(strpos($property,':') > strpos($property,';')) {    
        if(preg_match_all('/([A-Z-]+)=([^;:]*)[:|;]?/s',$property,$matches,PREG_SET_ORDER)) {
            //print_r($matches);
            for($i=0,$max=count($matches); $i<$max; $i++) {
                $attribute = $assign_to.'_'.$matches[$i][1];
                $attribute = strtolower($attribute);
                $attribute = str_replace('-','_',$attribute);
                if($n >= 0) {
                    $vproperty->{$attribute}[$n] = $matches[$i][2];
                } else {
                    $vproperty->{$attribute} = $matches[$i][2];
                }
                // remove the attribute from the string
                if($n >= 0) {
                    $vproperty->{$assign_to}[$n] = str_replace($matches[$i][0],'',$property);
                } else {
                    $vproperty->{$assign_to} = str_replace($matches[$i][0],'',$property);
                }
                unset($attribute);
            }
        }
        
    }
    /*
    
    
    if(preg_match('/ALTREP="(.*)"[:|;]/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_altrep";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        if($n >= 0) {
            $vproperty->{$assign_to}[$n] = str_replace($matches[0],'',$property);
        } else {
            $vproperty->{$assign_to} = str_replace($matches[0],'',$property);
        }
        unset($attribute);
    }

    if(preg_match('/LANGUAGE=([^;:]*)[:|;]/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_language";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        if($n >= 0) {
            $vproperty->{$assign_to}[$n] = str_replace($matches[0],'',$property);
        } else {
            $vproperty->{$assign_to} = str_replace($matches[0],'',$property);
        }
        unset($attribute);
    }

    if(preg_match('/TZID=([^;:]*)[:|;]/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_tzid";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        if($n >= 0) {
            $vproperty->{$assign_to}[$n] = str_replace($matches[0],'',$property);
        } else {
            $vproperty->{$assign_to} = str_replace($matches[0],'',$property);
        }
        unset($attribute);
    }

    if(preg_match('/VALUE=([^;:]*)[:|;]/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_value";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        if($n >= 0) {
            $vproperty->{$assign_to}[$n] = str_replace($matches[0],'',$property);
        } else {
            $vproperty->{$assign_to} = str_replace($matches[0],'',$property);
        }
        unset($attribute);
    }
    */
    
    /**
     *  These are recurrence rule attributes
     *  We don't necessarily want to remove them the the *rule property when done
     *
    
    if(preg_match('/FREQ=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_freq";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }

    if(preg_match('/UNTIL=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_until";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }
    
    if(preg_match('/COUNT=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_count";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }

    if(preg_match('/INTERVAL=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_interval";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }

    if(preg_match('/BYSECOND=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_bysecond";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }

    if(preg_match('/BYMINUTE=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_byminute";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }
    
    if(preg_match('/BYHOUR=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_byhour";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }

    if(preg_match('/BYDAY=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_byday";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }
    
    if(preg_match('/BYMONTHDAY=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_bymonthday";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }
    
    if(preg_match('/BYYEARDAY=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_byyearday";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }
    
    if(preg_match('/BYWEEKNO=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_byweekno";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }

    if(preg_match('/BYMONTH=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_bymonth";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }

    if(preg_match('/BYSETPOS=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_bysetpos";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }

    if(preg_match('/WKST=([^;:]*)[:|;]?/s',$property,$matches)) {
        //print_r($matches);
        $attribute = "{$assign_to}_wkst";
        if($n >= 0) {
            $vproperty->{$attribute}[$n] = $matches[1];
        } else {
            $vproperty->{$attribute} = $matches[1];
        }
        // remove the altrep from the string
        unset($attribute);
    }

    /**/

    
    
}
?>