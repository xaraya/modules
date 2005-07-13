<?
// -----------------------------------------------------------------------------------------------
// textdb.php
// -----------------------------------------------------------------------------------------------
// Project:   Database with csv files
// Author:    Copyright (c) 2002 Ruthger Gubbels. 
// Mail:      ruthger@softhome.net
// Version:   1.1.0
// Update:    2002-07-26
// Licence:   Freeware
//
// Source:    http://www.phpclasses.org/browse.html/package/623.html
// Reference: Crypting functions          written by: Unknown
// -----------------------------------------------------------------------------------------------
// Version 1.1 - 2002-07-26
// - Crypting data          New
// - Less memory usage      Updated
// - Function Sort          Updated
//
// Version 1.0 - 2002-06-10
// - First release

// Todo
// - Filelocking
// -----------------------------------------------------------------------------------------------

  class textDB
  {
    var $file;
    var $fieldNamesRec;
    var $fieldNames;
    var $records;
    var $recordNr;
    var $keys;
    var $keyNames;
    
    var $sort   = false;
    var $filter = false;
    var $buffer = false;

    var $crypt = false;
    var $cryptKey;
    
    function textDB($file, $cryptKey = "")
    {
      $this->file = $file;
      if ($cryptKey)
      {
        $this->crypt = true;
        $this->cryptKey = $cryptKey;
      }
      $this->connect();
      
    }

    function keyED($txt,$encrypt_key)
    { 
      $encrypt_key = md5($encrypt_key);
      $ctr=0; $tmp = "";
      for ($i=0;$i < strlen($txt);$i++) 
      { 
        if ($ctr==strlen($encrypt_key)) 
          $ctr=0; 
        $tmp.= substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1); 
        $ctr++; 
      } 
      return $tmp; 
    } 

    function encrypt($txt,$key) 
    { 
      srand((double)microtime()*1000000); 
      $encrypt_key = md5(rand(0,32000)); 
      $ctr=0; 
      $tmp = ""; 
      for ($i=0;$i < strlen($txt);$i++) 
      { 
        if ($ctr==strlen($encrypt_key)) 
          $ctr=0; 
        $tmp.= substr($encrypt_key,$ctr,1) . (substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1)); 
        $ctr++;
      } 
      return base64_encode($this->keyED($tmp,$key)); 
    } 
  
    function decrypt($txt,$key) 
    { 
      $txt = $this->keyED(base64_decode($txt),$key); 
      $tmp = ""; 
      for ($i=0;$i < strlen($txt);$i++) 
      { 
        $md5 = substr($txt,$i,1); 
        $i++; 
        $tmp.= (substr($txt,$i,1) ^ $md5); 
      } 
      return $tmp; 
    } 
   
    function connect()
    {
      $rawdata = @file($this->file);
      
      $fieldNamesRec = ereg_replace("(\r)|(\n)","",$rawdata[0]);;
      $fieldNames = explode(";", $fieldNamesRec);
      @array_shift($rawdata);
      
      while (list ($fieldNr, $fieldName) = each ($fieldNames))
      {
        if ($fieldName == "")
      	{
      	  unset($fieldNames[$fieldNr]);
      	}
      	
      	if (substr($fieldName, 0, 1) == "*")
      	{
      	  $keyNames[] = substr($fieldName, 1);
      	  $fieldNames[$fieldNr] = substr($fieldName, 1);
      	}
      }

      $recordNr = 0;
      while ($rawdata)
      {
	$recordRaw = array_shift($rawdata);
      	if (strlen($recordRaw)>1)
      	{ 
	  //echo ("RecordRaw: $recordRaw, length:" . strlen($recordRaw)."<BR>");
	  $recordNr++;
	  if ($this->crypt)
            $recordRaw = $this->decrypt($recordRaw, $this->cryptKey);
	  
          $recordFields = explode(";",$recordRaw);
	  
          $record = "";
          for ($fieldNr = 0; $fieldNr < (count($fieldNames)) ;$fieldNr++)
	    {
              $fieldName = $fieldNames[$fieldNr];
	      $record[$fieldName] = trim(ereg_replace('&semi&',';',$recordFields[$fieldNr]));
	    }
          $records[$recordNr] = $record;
	  
          unset($key);
          for ($keyNr = 0; $keyNr < (count($keyNames)) ;$keyNr++)
	    {
	      
	      $fieldName = $keyNames[$keyNr];
	      $key .= ereg_replace(";",'&semi&',$record[$fieldName]).";";
	      
	    }
          $keys[$key] = $recordNr;
        }
      }
      
      $this->fieldNames    = $fieldNames;
      $this->keyNames      = $keyNames;
      $this->records       = $records;
      $this->keys          = $keys;
      $this->fieldNamesRec = $fieldNamesRec;
      $this->recordNr      = 0;

      return $records;
    }
  
    function flush()
    {
      $recordFile = fopen($this->file, "w");
     
      $fieldNamesRec = $this->fieldNamesRec."\r\n";
      fwrite($recordFile, $fieldNamesRec);
      
      reset($this->records);
      while (list ($recordNr, $record) = each ($this->records)) 
      {
      	unset($recordRaw);
        for ($fieldNr = 0; $fieldNr < (count($this->fieldNames)) ;$fieldNr++)
        {
          $fieldName = $this->fieldNames[$fieldNr];
          $record[$fieldName] = ereg_replace(";",'&semi&',$record[$fieldName]);
        }
        $recordRaw = ereg_replace("\r\n",'<br>',implode(";", $record));

        if ($this->crypt)
          $recordRaw = $this->encrypt($recordRaw, $this->cryptKey);
        
        $recordRaw .= "\r\n";
        fwrite($recordFile, $recordRaw);
      }
      fclose($recordFile);
    }

    function first()
    {
      if (isset($this->keys) and $this->recordNr = reset($this->keys))
        return $this->records[$this->recordNr];
      else
        return false;
    }
    
    function prev()
    {
      if (isset($this->keys) and $this->recordNr = prev($this->keys))
        return $this->records[$this->recordNr];
      else
        return false;
    }

    function next()
    {
      if (isset($this->keys) and $this->recordNr = next($this->keys))
        return $this->records[$this->recordNr];
      else
        return false;
    }

    function last()
    {
      if (isset($this->keys) and $this->recordNr = end($this->keys))
        return $this->records[$this->recordNr];
      else
        return false;
    }

    function insert($record)
    {
      if (!$this->filter)
      {
        $this->records[] = $record;
        if (!$this->buffer)
          $this->flush();
        $this->sort($this->sort);
      }
    }

    function update($record)
    {
      if (!$this->filter and $this->recordNr != 0)
      {
        $this->records[$this->recordNr] = $record;
        if (!$this->buffer)
          $this->flush();
        $this->sort($this->sort);
      }
    }    

    function delete()
    {
      if (isset($this->keys) and !$this->filter and $this->recordNr != 0)
      {
        unset($this->records[$this->recordNr]);
        if (!$this->buffer)
          $this->flush();
        $this->sort($this->sort);
      }
    }

    function search($record)
    {
      for ($keyNr = 0; $keyNr < (count($this->keyNames)) ;$keyNr++)
      {
        $fieldName = $this->keyNames[$keyNr];
        $key .= $record[$fieldName].";";
      }
      $this->recordNr = $this->keys[$key];
      return $this->records[$this->recordNr];
     
    }    

    function sort($sort = false, $keyNames = "")
    {
      if ($keys == "")
      {
      	unset($this->keyNames);
        $fieldNames = explode(";", $this->fieldNamesRec);
        while (list ($fieldNr, $fieldName) = each ($fieldNames))
        {
          if (substr($fieldName, 0, 1) == "*")
      	  {
      	    $this->keyNames[] = substr($fieldName, 1);
          }
        }
      }
      else
      {
      	unset($this->keyNames);
        $this->keyNames = explode(";", $keys);
      }

      unset($this->keys);
      reset($this->records);
      while (list ($recordNr, $record) = each ($this->records))
      {
        unset($key);
        for ($keyNr = 0; $keyNr < (count($this->keyNames)) ;$keyNr++)
        {
          $fieldName = $this->keyNames[$keyNr];
          $key .= $record[$fieldName].";";
        }
        $this->keys[$key] = $recordNr;
      }
      
      if (($sort == "A" or $sort == "D") and count($this->records) > 0)
      {
        reset($this->keys);
        while (list ($key, $recordNr) = each ($this->keys))
        {
          $keys[$recordNr] = explode(";", $key);
        }
        unset($this->keys); 
        if ($sort == "A")
          arsort($keys);
        else if ($sort == "D")
          asort($keys);      
        while (list ($recordNr, $key) = each ($keys))
        {
          $this->keys[implode(";", $key)] = $recordNr;
        }
      }
      $this->sort = $sort;
    }

    function filter($filter = false)
    {
      $this->connect();
      if ($filter and count($this->records) > 0)
      {
        $filter = 'if ('.$filter.') $filtered=false; else $filtered=true;';      
        
        reset($this->fieldNames);
        while (list ($fieldNr, $fieldName) = each ($this->fieldNames))
        {
          $filter = ereg_replace($fieldName, '$record['.$fieldName.']', $filter);
        }
        
        reset($this->records);
        while (list ($recordNr, $record) = each ($this->records))
        {
          eval($filter);
          if ($filtered)
          {
            unset($this->records[$recordNr]);
          }
        } 
      } 	
      $this->sort($this->sort, $this->keynames);
      $this->filter = $filter;
    }
  }
?>