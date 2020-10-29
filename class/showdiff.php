<?php
/**
 * PSSPL:Added the code for formating the difference.
 * Get The diff between two string and apply the formating.
*/
//Psspl:Define the character sequences

define('ADDED_GREEN_START_TAG', '@~$#%*^');
define('ADDED_GREEN_END_TAG', '^%*$@~#');
define('DELETED_BLUE_START_TAG', '$#~@%^&*');
define('DELETED_BLUE_END_TAG', '*^%$~#@');
define('COPY_WHITE_START_TAG', '#$@*^~');
define('COPY_WHITE_END_TAG', '*^%$%~@');
define('CHANGED_RED_START_TAG', '~*^%$#@');
define('CHANGED_RED_END_TAG', '!~$%^&#');

//Psspl:Define the color code
define('ADDED_BGCOLOR', '#99FF99');//grren
define('DELETED_BGCOLOR', '#6699FF');//blue
define('CHANGED_BGCOLOR', '#FF9999');//red
define('COPY_BGCOLOR', 'white');//white

class showdiff
{
    public $add_orig_array = array();
    public $add_final_array = array();
    public $delete_orig_array = array();
    public $delete_final_array = array();
    public $change_orig_array = array();
    public $change_final_array = array();
    public $copy_orig_array = array();
    public $copy_final_array = array();
    public $oldLineCheckFlag = array();
    public $newLineCheckFlag = array();
    public $highlight_old_arr = array();
    public $highlight_new_arr = array();
    public $orig_array = array();
    public $final_array = array();
    public $highlight_old ;
    public $highlight_new ;

    public function checkdiff($oldString, $newString, $diff, $Flag)
    {
        /*<!--<style><span style="background-color: #FFFF00">
            .highlight_word_added{
            background-color: #99FF99;<!--color:green:
            }
            .highlight_word_deleted{
                background-color: #6699FF;<!--color:blue->
            }
            .highlight_word_copy{
                background-color: white;<!--color:white--
            }
            .highlight_word_changed{
                background-color: #FF9999;<!--color:red--
            }
            </style>-->
        */
        foreach ($diff as $key => $valueArray) {
            foreach ($valueArray as $key => $val) {
                if ($val->type == 'add') {
                    if ($val->orig) {
                        $this->add_orig_array = array_merge($this->add_orig_array, $val->orig);
                    }
                    if ($val->final) {
                        $this->add_final_array = array_merge($this->add_final_array, $val->final);
                    }
                }
                if ($val->type == 'delete') {
                    if ($val->orig) {
                        $this->delete_orig_array = array_merge($this->delete_orig_array, $val->orig);
                    }
                    if ($val->final) {
                        $this->delete_final_array = array_merge($this->delete_final_array, $val->final);
                    }
                }
                if ($val->type == 'change') {
                    if ($val->orig) {
                        $this->change_orig_array = array_merge($this->change_orig_array, $val->orig);
                    }
                    if ($val->final) {
                        $this->change_final_array = array_merge($this->change_final_array, $val->final);
                    }
                }
                if ($val->type == 'copy') {
                    if ($val->orig) {
                        $this->copy_orig_array = array_merge($this->copy_orig_array, $val->orig);
                    }
                    if ($val->final) {
                        $this->copy_final_array = array_merge($this->copy_final_array, $val->final);
                    }
                }
            }
        }
        if ($Flag =='Line') {
            $this->highlightLines($oldString, $newString);
            
            $this->orig_array = $this->change_orig_array;
            $this->final_array = $this->change_final_array;
            
            $this->checkChangeWords();
        } else {
            $this->highlightWords($oldString, $newString);
        }
        
        $this->highlight_old = "";
        foreach ($this->highlight_old_arr as $line) {
            $this->highlight_old .= "\n".$line;
        }
        
        $this->highlight_new = "";
        foreach ($this->highlight_new_arr as $line) {
            $this->highlight_new .= "\n".$line;
        }
        
        $ReturnString=$this->highlight_old."<br>".$this->highlight_new;
        
        return $ReturnString;
    }
    
    public function checkChangeWords()
    {
        foreach ($this->final_array as $key => $final_string) {
            $orig_string = isset($this->orig_array[$key])? $this->orig_array[$key]: null;
            $orig_string = $this->check_lastcharacter($orig_string);
            $final_string = isset($final_string)? $final_string: null;
            $diff = new Diff(explode(" ", $orig_string), explode(" ", $final_string));
            //$diff = new Diff( explode(" ",$this->orig_array[$key]), explode(" ",$final_string));
            
            $this->add_orig_array = array();
            $this->add_final_array = array();
            $this->delete_orig_array = array();
            $this->delete_final_array =array();
            $this->change_orig_array = array();
            $this->change_final_array =array() ;
            $this->copy_orig_array = array();
            $this->copy_final_array = array();
            
            $orig_string = isset($this->orig_array[$key])? $this->orig_array[$key]: null;
            $orig_string = $this->check_lastcharacter($orig_string);
            $final_string = $this->final_array[$key];
            $final_string = isset($final_string)? $final_string: null;
            $this->checkdiff($orig_string, $final_string, $diff, 'Words');
        }
    }
    
    public function highlightWords_orig($string_old, $changed, $deleted)
    {
        $string = explode(" ", $string_old);
        
        $StringTemp = $string;
        $checkFlag = $string;
        
        $words = $deleted;
        foreach ($words as $keyTop => $word) {
            foreach ($string as $key => $strItem) {
                if ($word == $strItem && $checkFlag[$key]!=false) {
                    $checkFlag[$key] = false;
                    $strItem = str_ireplace($word, DELETED_BLUE_START_TAG.$word.DELETED_BLUE_END_TAG, $strItem);
                    $StringTemp[$key] = $strItem;
                    break;
                }
            }
        }
        
        $words = $changed;
        foreach ($words as $keyTop => $word) {
            foreach ($string as $key => $strItem) {
                if ($word == $strItem && $checkFlag[$key] != false) {
                    $strItem = str_ireplace($word, DELETED_BLUE_START_TAG.$word.DELETED_BLUE_END_TAG, $strItem);
                    
                    $word = isset($this->change_final_array[$keyTop])?$this->change_final_array[$keyTop]:null;
                    
                    $checkFlag[$key] = false;
                    $strItem = str_ireplace($word, COPY_WHITE_START_TAG.$word.COPY_WHITE_END_TAG, $strItem);
                    $StringTemp[$key] = $strItem;
                    break;
                }
            }
        }
    
        $str_highlighted = "";
        foreach ($StringTemp as $str) {
            $str_highlighted .= $str." ";
        }
        $str_highlighted = str_ireplace(DELETED_BLUE_START_TAG, '<span style="background-color:'.DELETED_BGCOLOR.'">', $str_highlighted);
        $str_highlighted = str_ireplace(DELETED_BLUE_END_TAG, '</span>', $str_highlighted);
        
        $str_highlighted = str_ireplace(COPY_WHITE_START_TAG, '<span style="background-color:'.COPY_BGCOLOR.'">', $str_highlighted);
        $str_highlighted = str_ireplace(COPY_WHITE_END_TAG, '</span>', $str_highlighted);
        
        foreach ($this->highlight_old_arr as $key =>$Line) {
            if ($this->oldLineCheckFlag[$key]) {
                $this->highlight_old_arr[$key]=str_ireplace($string_old, $str_highlighted, $Line);
            }
        }
        
        return $str_highlighted;
    }
    
    public function highlightWords_final($string_new, $changed, $added)
    {
        $string = explode(" ", $string_new);
        $StringTempNew = $string;
        $checkFlag = $string;
        $words = $changed;
        
        foreach ($words as $word) {
            foreach ($string as $key => $strItem) {
                if ($word == $strItem && $checkFlag[$key]!=false) {
                    $checkFlag[$key] = false;
                    $strItem = str_ireplace($word, CHANGED_RED_START_TAG.$word.CHANGED_RED_END_TAG, $strItem);
                    $StringTempNew[$key] = $strItem;
                    break;
                }
            }
        }
        $words = $added;
        foreach ($words as $word) {
            foreach ($string as $key => $strItem) {
                if ($word==$strItem && $checkFlag[$key]!=false) {
                    $checkFlag[$key]=false;
                    $strItem = str_ireplace($word, ADDED_GREEN_START_TAG.$word.ADDED_GREEN_END_TAG, $strItem);
                    $StringTempNew[$key] = $strItem;
                    break;
                }
            }
        }
    
        $str_highlighted="";
        
        foreach ($StringTempNew as $str) {
            $str_highlighted .= $str." ";
        }
        
        $str_highlighted = str_ireplace(CHANGED_RED_START_TAG, '<span style="background-color:'.CHANGED_BGCOLOR.'">', $str_highlighted);
        $str_highlighted = str_ireplace(CHANGED_RED_END_TAG, '</span>', $str_highlighted);
    
        $str_highlighted = str_ireplace(ADDED_GREEN_START_TAG, '<span style="background-color:'.ADDED_BGCOLOR.'">', $str_highlighted);
        $str_highlighted = str_ireplace(ADDED_GREEN_END_TAG, '</span>', $str_highlighted);
        
        foreach ($this->highlight_new_arr as $key => $Line) {
            if ($this->newLineCheckFlag[$key]) {
                $this->highlight_new_arr[$key] = str_ireplace($string_new, $str_highlighted, $Line);
            }
        }
        
        return $str_highlighted;
    }
    
    public function highlightLine_delete($string, $words)
    {
        $StringTemp = $string;
        foreach ($words as $word) {
            foreach ($string as $key => $strItem) {
                if ($word == $strItem && $this->oldLineCheckFlag[$key] != false) {
                    $this->oldLineCheckFlag[$key] = false;
                    $strItem = str_ireplace($word, DELETED_BLUE_START_TAG.$word.DELETED_BLUE_END_TAG, $strItem);
                    $StringTemp[$key] = $strItem;
                    break;
                }
            }
        }
    
        $str_highlighted = "";
        foreach ($StringTemp as $str) {
            $str_highlighted .= $str."\n";
        }
        
        $str_highlighted = str_ireplace(DELETED_BLUE_START_TAG, '<span style="background-color:'.DELETED_BGCOLOR.'">', $str_highlighted);
        $str_highlighted = str_ireplace(DELETED_BLUE_END_TAG, '</span>', $str_highlighted);
    
        $str_highlighted = substr($str_highlighted, 0, strlen($str_highlighted)-1);
        
        return $str_highlighted;
    }
    
    public function highlightLine_added($string, $words)
    {
        $StringTempNew = $string;
        foreach ($words as $word) {
            foreach ($string as $key => $strItem) {
                if ($word == $strItem && $this->newLineCheckFlag[$key]!=false) {
                    $this->newLineCheckFlag[$key] = false;
                    $strItem = str_ireplace($word, ADDED_GREEN_START_TAG.$word.ADDED_GREEN_END_TAG, $strItem);
                    $StringTempNew[$key] = $strItem;
                    break;
                }
            }
        }
    
        $str_highlighted = "";
    
        foreach ($StringTempNew as $str) {
            $str_highlighted .= $str."\n";
        }
    
        $str_highlighted = str_ireplace(ADDED_GREEN_START_TAG, '<span style="background-color:'.ADDED_BGCOLOR.'">', $str_highlighted);
        $str_highlighted = str_ireplace(ADDED_GREEN_END_TAG, '</span>', $str_highlighted);
    
        $str_highlighted = substr($str_highlighted, 0, strlen($str_highlighted)-1);
        return $str_highlighted;
    }
    
    public function Line_copy_Old($string, $words)
    {
        foreach ($words as $word) {
            foreach ($string as $key => $strItem) {
                $this->newLineCheckFlag[$key] = isset($this->newLineCheckFlag[$key])?$this->newLineCheckFlag[$key]:null;
                if ($word == $strItem && $this->newLineCheckFlag[$key] != false) {
                    $this->oldLineCheckFlag[$key]=false;
                    break;
                }
            }
        }
        return true;
    }
    
    public function Line_copy_New($string, $words)
    {
        foreach ($words as $word) {
            foreach ($string as $key => $strItem) {
                if ($word == $strItem && $this->newLineCheckFlag[$key] != false) {
                    $this->newLineCheckFlag[$key] = false;
                    break;
                }
            }
        }
        return true;
    }
    
    public function highlightLines($string_old, $string_new)
    {
        $string_old = explode("\n", $string_old);
        $string_new = explode("\n", $string_new);
    
        $this->highlight_old_arr = $string_old;
        $this->highlight_new_arr = $string_new;
        $this->oldLineCheckFlag = $string_old;
        $this->newLineCheckFlag = $string_new;
            
        $this->Line_copy_Old($string_old, $this->copy_orig_array);
        $this->Line_copy_New($string_new, $this->copy_orig_array);
    
        $this->highlight_old = $this->highlightLine_delete($string_old, $this->delete_orig_array);
        $this->highlight_new =  $this->highlightLine_added($string_new, $this->add_final_array);
    
        $this->highlight_old_arr = explode("\n", $this->highlight_old);
        $this->highlight_new_arr = explode("\n", $this->highlight_new);
    }
    
    public function highlightWords($string_old, $string_new)
    {
        $this->highlightWords_orig($string_old, $this->change_orig_array, $this->delete_orig_array);
        $this->highlightWords_final($string_new, $this->change_final_array, $this->add_final_array);
    }
    
    public function displayDiff()
    {
        ?><html>
			<head> 
				<title>Highlight Search Words</title> 
			</head>
			<body>
				<table border="1">
					<tr>
						<th>OldVersion</th>
						<th>NewVersion</th>
					</tr>
					<tr>
						<td>
							<?php print_r("<pre>$this->highlight_old</pre>"); ?>
						</td>
						<td>
							<?php print_r("<pre>$this->highlight_new</pre>"); ?>
						</td>
					</tr>
				</table>
			</body>
		</html>
		<?php
    }
    public function check_lastcharacter($str)
    {
        $oldstr_lastcharcter = substr($str, -1, 1);
        $str_len = strlen($str);
    
        if ($oldstr_lastcharcter=="\n") {
            $str = substr($str, 0, strlen($str)-1);
            $str = $this->check_lastcharacter($str);
        }
        if ($oldstr_lastcharcter == " ") {
            $str = substr($str, 0, strlen($str)-1);
            $str = $this->check_lastcharacter($str, $count+1);
        }
        return $str;
    }
}
