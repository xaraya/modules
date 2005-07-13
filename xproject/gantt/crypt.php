Crypt example....

Insert a record...<br>
<?
  include("textdb.php");
  
  $test = New textDB("test_crypt.txt", "fe54ksdfk");
  
  $record = $test->first();
  while ($record)
  {
    echo $record[id]." ".$record[label]." ".$record[text]."<br>";
    $record = $test->next();
  }

  $record[id]    = 12;
  $record[label] = "c54332";
  $record[text]  = "If this works";

  $test->insert($record);      

  $record = $test->first();
  while ($record)
  {
    echo "Result : ".$record[id]." ".$record[label]." ".$record[text]."<br>";
    $record = $test->next();
  }

?>
