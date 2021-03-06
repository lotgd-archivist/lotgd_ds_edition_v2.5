<?php

function publicProcessHandler($str, $indent)
{
    // placeholders prevent strings and comments from being processed
    preg_match_all("/\/\*.*?\*\/|\/\/[^\n]*|#[^\n]|([\"'])[^\\\\]*?(\\\\.[^\\\\]*?)*?\\1/s", $str, $matches);
    $matches[0]=array_values(array_unique($matches[0]));
    for ($i=0;$i<count($matches[0]);$i++){
        $patterns[]="/".preg_quote($matches[0][$i], '/')."/";
        $placeholders[]="'placeholder$i'";
        // double backslashes must be escaped if we want to use them in the replacement argument
        $matches[0][$i]=str_replace('\\\\', '\\\\\\\\', $matches[0][$i]);
    }

    if ($placeholders){
        $str=preg_replace($patterns, $placeholders, $str);
    }

    //parsing and indenting
    $str=privateIndentParsedString(privateParseString($str), $indent);

    // insert original strings and comments
    for ($i=count($placeholders)-1;$i>=0;$i--){
        $placeholders[$i]="/".$placeholders[$i]."/";
    }

    if ($placeholders){
        $str=preg_replace($placeholders, $matches[0], $str);

    }
    return $str;
}

function privateParseString($str)
{
    // inserting missing braces (does only match up to 2 nested parenthesis)
    $str=preg_replace("/(if|for|while|switch)\s*(\([^()]*(\([^()]*\)[^()]*)*\))([^{;]*;)/i", "\\1 \\2 {\\4\n}", $str);
    // missing braces for else statements
    $str=preg_replace("/(else)\s*([^{;]*;)/i", "\\1 {\\2\n}", $str);

    // line break check
    $str=preg_replace("/([;{}]|case\s[^:]+:)\n?/i", "\\1\n", $str);
    $str=preg_replace("/^function\s+([^\n]+){/mi", "function \\1\n{", $str);

    // remove inserted line breaks at else and for statements (no!)
    $str=preg_replace("/}\s*else\s*/m", "}\n else ", $str);
    $str=preg_replace("/(for\s*\()([^;]+;)(\s*)([^;]+;)(\s*)/mi", "\\1\\2 \\4 ", $str);

    // remove spaces between function call and parenthesis and start of argument list
    $str=preg_replace("/(\w+)\s*\(\s*/", "\\1(", $str);

    // adds line breaks between condition and brace
    // set one space between control keyword and condition
    $str=preg_replace("/(if|for|while|switch|elseif|else if|foreach)\s*(\([^{]+\))\s*{/i", "\\1 \\2 \n{", $str);
	
	// same for else
	$str=preg_replace("/(else)\s*{/i", "\\1 \n{", $str);
	
	// string keys in arrays: put them into quotes
	$str=preg_replace("/\[([\w][a-z0-9_][\w]*)\]/", "['\\1']", $str);
	
	// replace HTTP_.._VARS with newer equivalents
	$str = str_replace('HTTP_GET_VARS','_GET',$str);
	$str = str_replace('HTTP_POST_VARS','_POST',$str);
	$str = str_replace('HTTP_COOKIE_VARS','_COOKIE',$str);
	$str = str_replace('HTTP_SESSION_VARS','_SESSION',$str);

    return $str;
}

function privateIndentParsedString($str, $indent)
{
    $count = substr_count($str, '}')-substr_count($str, '{');
    if ($count<0){
        $count = 0;
    }

    $strarray=explode("\n", $str);

    for($i=0;$i<count($strarray);$i++){
        $strarray[$i]=trim($strarray[$i]);
        if (strstr($strarray[$i], '}')){
            $count--;
        }
        if (preg_match("/^case\s/i", $strarray[$i])){
            $level=str_repeat(" ", $indent*($count-1));
        } else if (preg_match("/^or\s/i", $strarray[$i])){
            $level=str_repeat(" ", $indent*($count+1));
        } else {
            $level=str_repeat(" ", $indent*$count);
        }
        $strarray[$i]=$level.$strarray[$i];
        if (strstr($strarray[$i], '{')){
            $count++;
        }
    }
    $parsedstr=implode("\n", $strarray);
    return $parsedstr;
}
?>