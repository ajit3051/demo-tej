<?php
class validation
{

	public function generateRandomAlphaNumericString($randStringLength = 6)
	{
		$charStr = "abcdefghijklmnopqrstuvwxyz0123456789";
		if($randStringLength) 
		{ 
			$randStr="";
			for($i=1; $i<=$randStringLength; $i++)
			{
			   mt_srand((double)microtime() * 1000000);
			   $randNum = mt_rand(0,strlen($charStr)-1);
			   $randStr .= $charStr[$randNum];
			   //$randStr .= $charStr{$randNum};
			}
			
			return $randStr;
		}
		return NULL;
	}
	
		
	public function escapeStringMySQL($str, $DB){
		
		/* if(get_magic_quotes_gpc()) // get_magic_quotes_gpc() should be disabled on server
		{ 
			return mysql_real_escape_string(stripslashes($str), $DB); // remove the default quotes
		} 
		else       
			return mysql_real_escape_string($str, $DB); 
		} */
	}
	
	
	
	public function isValidUserID($str)
	{
		
		//$goodchar = "abcdefghijklmnopqrstuvwxyz0123456789-_.@";
		
		$goodchar = "abcdefghijklmnopqrstuvwxyz0123456789-_.";


		for ($i=0;$i<strlen($str);$i++)
		{
				if (gettype(strpos($goodchar,strtolower($str[$i]))) == 'boolean') // boolean false is returned if needle is not found
				{
					return false; 
				}
	
		}
		
		return true;
		
	}
	
	
	public function isValidPassword($str)
	{
		
		$goodchar = "abcdefghijklmnopqrstuvwxyz0123456789 -+=_.?@$%/\&*,;:(){}[]`~!^|";


		for ($i=0;$i<strlen($str);$i++)
		{
			//if (!($str[$i] == " "))
			//{
				if (gettype(strpos($goodchar,strtolower($str[$i]))) == 'boolean') // boolean false is returned if needle is not found
				{
					return false; // bad character present
				}
			//}
		}
		
		return true;
		
	}
	
	
	public function isValidPhone($str)
	{
		
		if(strlen($str)>13 || strlen($str)<8)
		{
	
			return false;
		}
		else
		{
		
			$goodchar = "0123456789 -+";
	
	
			for ($i=0;$i<strlen($str);$i++)
			{
				//if (!($str[$i] == " "))
				//{
					if (gettype(strpos($goodchar,strtolower($str[$i]))) == 'boolean') // boolean false is returned if needle is not found
					{
						return false; // bad character present
					}
				//}
			}
			
			return true;
		}
		
	}
	
	public function isValidMobile($str)
	{
		
		if(strlen($str)!=10)
		{
			return false;
		}
		else
		{
		
			$goodchar = "0123456789";
	
	
			for ($i=0;$i<strlen($str);$i++)
			{
				
					if (gettype(strpos($goodchar,strtolower($str[$i]))) == 'boolean') // boolean false is returned if needle is not found
					{
						return false; // bad character present
					}
				
			}
			
			return true;
		}
		
	}
	
	
	public function badCharacterPresent ($str)  // old: chksplcharald
	{
		
		$goodchar = "abcdefghijklmnopqrstuvwxyz0123456789-+=_.?@$%/\&*,;:'(){}[]`~!^|\t\r\n";


		for ($i=0;$i<strlen($str);$i++)
		{
			//if (!($str[$i] == " "))
			//{
				if (gettype(strpos($goodchar,strtolower($str[$i]))) == 'boolean') // boolean false is returned if needle is not found
				{
					
					return true; // bad character present 

				}
			//}
		}
		
		return false;
		
	}
	
	public function filterText($str="", $removeHTMLTags=0){
		
		if(strlen($str))
		{
			if($removeHTMLTags==1)
			{
				
				// htmlentities
				// htmlspecialchars
				
				$str=htmlspecialchars(strip_tags(trim($str)),ENT_QUOTES);
				
			
				
				//$str=htmlentities(strip_tags(trim($str)));
			}
			else
			{
				
				// htmlentities
				// htmlspecialchars
				$str=htmlspecialchars(trim($str),ENT_QUOTES);
				
				//$str=htmlentities(trim($str));
			}
		}
		
		$str=str_replace('%','percent',$str);
		
		return $str;
	}
	
	
	
	public function isValidText($str, $feildName='') // call it after calling fiterText() over text input string
	{
		$message=array();
		
		
		if(preg_match("/-\s*-/i", $str)) {
		 $message[]=htmlentities("'--' string is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		if(preg_match("/onmouseover/i", $str)) {
		 $message[]=htmlentities("'onmouseover' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		if(preg_match("/onmouseout/i", $str)) {
		 $message[]=htmlentities("'onmouseout' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		
		if(preg_match("/onmouseenter/i", $str)) {
		 $message[]=htmlentities("'onmouseenter' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		if(preg_match("/onmouseleave/i", $str)) {
		 $message[]=htmlentities("'onmouseleave' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		if(preg_match("/onmouseup/i", $str)) {
		 $message[]=htmlentities("'onmouseup' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		if(preg_match("/onmousedown/i", $str)) {
		 $message[]=htmlentities("'onmousedown' string is not allowed in $feildName.",ENT_QUOTES);
		}
	
		return $message;
	}
	

	public function isValidHTML($str, $feildName='')
	{
		
		$message=array();
	
		if(preg_match("/<\s*iframe\s*/", $str)) {
		 $message[]=htmlentities("'<iframe' tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		
		if(preg_match("/<\s*\/\s*iframe\s*>/i", $str)) {
		 $message[]=htmlentities("'</iframe>' tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		
		if(preg_match("/<\s*a\s*/i", $str)) {
		  $message[]=htmlentities("'<a' tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		if(preg_match("/<\s*\/\s*a\s*>/i", $str)) {
		 $message[]=htmlentities("'</a>' tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		
		if(preg_match("/<\s*img\s*/i", $str)) {
		  $message[]=htmlentities("'<img' tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		
		if(preg_match("/<\s*script\s*/", $str)) {
		 $message[]=htmlentities("'<script' tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		if(preg_match("/<\s*\/\s*script\s*>/i", $str)) {
		 $message[]=htmlentities("'</script>' tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		if(preg_match("/<\s*input/i", $str)) {
		 $message[]=htmlentities("'<input' tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		if(preg_match("/<\s*textarea/i", $str)) {
		 $message[]=htmlentities("'<textarea' tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		if(preg_match("/<\s*\/\s*textarea\s*>/i", $str)) {
		 $message[]=htmlentities("'</textarea>' tag is not allowed in $feildName.",ENT_QUOTES);
		} 
	
		
		if(preg_match("/\/\s*\*/i", $str)) {
		 $message[]=htmlentities("'/*' comment tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		if(preg_match("/\*\s*\//i", $str)) {
		 $message[]=htmlentities("'*/' comment tag is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		if(preg_match("/-\s*-/i", $str)) {
		 $message[]=htmlentities("'--' string is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		if(preg_match("/<\s*!\s*-\s*-/i", $str)) {
		 $message[]=htmlentities("'<!--' string is not allowed in $feildName.",ENT_QUOTES);
		} 
		
		
		if(preg_match("/-\s*-\s*>/i", $str)) {
		 $message[]=htmlentities("'-->' string is not allowed in $feildName.",ENT_QUOTES);
		} 
	
		
		if(preg_match("/alert\s*\(/i", $str)) {
		 $message[]=htmlentities("'alert(' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
	
		if(preg_match("/>\s*\"\s*>/i", $str)) {
		 $message[]=htmlentities("'>\">' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		if(preg_match("/\"\s*\"\s*>/i", $str)) {
		 $message[]=htmlentities("'\"\">' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		
		if(preg_match("/onmouseover/i", $str)) {
		 $message[]=htmlentities("'onmouseover' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		
		if(preg_match("/onmouseout/i", $str)) {
		 $message[]=htmlentities("'onmouseout' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		
		if(preg_match("/onmouseenter/i", $str)) {
		 $message[]=htmlentities("'onmouseenter' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		
		if(preg_match("/onmouseleave/i", $str)) {
		 $message[]=htmlentities("'onmouseleave' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		if(preg_match("/onmouseup/i", $str)) {
		 $message[]=htmlentities("'onmouseup' string is not allowed in $feildName.",ENT_QUOTES);
		}
		
		
		if(preg_match("/onmousedown/i", $str)) {
		 $message[]=htmlentities("'onmousedown' string is not allowed in $feildName.",ENT_QUOTES);
		}
	
		return $message;
	}
	




	//////////////////////////////////copied from drupal//////////////////////////
	
	
	// $allowed_tags = array('a', 'em', 'strong', 'cite', 'code', 'ul', 'ol', 'li', 'dl', 'dt', 'dd','table','td','tr','tbody','th','b','br','center','col','font','h1','h2','h3','h4','h5','h6','hr','i','p','span','textarea','thead','title','tt','u')
	
	public function filter_xss($string, $allowed_tags = array('em', 'strong', 'cite', 'code', 'ul', 'ol', 'li', 'dl', 'dt', 'dd','table','td','tr','tbody','th','b','br','center','col','font','h1','h2','h3','h4','h5','h6','hr','i','p','span','thead','title','tt','u')) {
	  // Only operate on valid UTF-8 strings. This is necessary to prevent cross
	  // site scripting issues on Internet Explorer 6.
	  $this->_filter_xss_split($allowed_tags, TRUE);
	  // Remove NUL characters (ignored by some browsers)
	  $string = str_replace(chr(0), '', $string);
	  // Remove Netscape 4 JS entities
	  $string = preg_replace('%&\s*\{[^}]*(\}\s*;?|$)%', '', $string);
	
	  // Defuse all HTML entities
	  $string = str_replace('&', '&amp;', $string);
	  // Change back only well-formed entities in our whitelist
	  // Decimal numeric entities
	  $string = preg_replace('/&amp;#([0-9]+;)/', '&#\1', $string);
	  // Hexadecimal numeric entities
	  $string = preg_replace('/&amp;#[Xx]0*((?:[0-9A-Fa-f]{2})+;)/', '&#x\1', $string);
	  // Named entities
	  $string = preg_replace('/&amp;([A-Za-z][A-Za-z0-9]*;)/', '&\1', $string);
	  
	  
	
	  return preg_replace_callback('%
		(
		<(?=[^a-zA-Z!/])  # a lone <
		|                 # or
		<!--.*?-->        # a comment
		|                 # or
		<[^>]*(>|$)       # a string that starts with a <, up until the > or the end of the string
		|                 # or
		>                 # just a >
		)%x', array(&$this, '_filter_xss_split'), $string);
	  
	  // second parameter of preg_replace_callback() above
	  // '_filter_xss_split' // orginal
	  // array(&$this, '_filter_xss_split') // oop version 1
	  // array(get_class($this), '_filter_xss_split') // oop version 2
	}
	
	
	
	public function _filter_xss_split($m, $store = FALSE) {
		
		
	  static $allowed_html;

	  if ($store) {
		$allowed_html = array_flip($m);
		//print_r($allowed_html); die;
		return;
	  }
	
	  $string = $m[1];
	
	  if (substr($string, 0, 1) != '<') {
		// We matched a lone ">" character
		return '&gt;';
	  }
	  else if (strlen($string) == 1) {
		// We matched a lone "<" character
		return '&lt;';
	  }
	
	  if (!preg_match('%^(?:<\s*(/\s*)?([a-zA-Z0-9]+)([^>]*)>?|(<!--.*?-->))$%', $string, $matches)) {
		// Seriously malformed
		return '';
	  }
	
	  $slash = trim($matches[1]);
	  $elem = &$matches[2];
	  $attrlist = &$matches[3];
	  $comment = &$matches[4];
	
	  if ($comment) {
		$elem = '!--';
	  }
	
	  if (!isset($allowed_html[strtolower($elem)])) {
		// Disallowed HTML element
		return '';
	  }
	
	  if ($comment) {
		return $comment;
	  }
	
	  if ($slash != '') {
		return "</".$elem.">";  // end of tag
	  }
	
	  // Is there a closing XHTML slash at the end of the attributes?
	  // In PHP 5.1.0+ we could count the changes, currently we need a separate match
	  $xhtml_slash = preg_match('%\s?/\s*$%', $attrlist) ? ' /' : '';
	  $attrlist = preg_replace('%(\s?)/\s*$%', '\1', $attrlist);
	
	  // Clean up attributes
	  $attr2 = implode(' ', $this->_filter_xss_attributes($attrlist));
	  $attr2 = preg_replace('/[<>]/', '', $attr2);
	  $attr2 = strlen($attr2) ? ' ' . $attr2 : '';
	
	  return "<$elem$attr2$xhtml_slash>";
	}
	
	
	
	public function _filter_xss_attributes($attr) {
	  $attrarr = array();
	  $mode = 0;
	  $attrname = '';
	
	  while (strlen($attr) != 0) {
		// Was the last operation successful?
		$working = 0;
	
		switch ($mode) {
		  case 0:
			// Attribute name, href for instance
			if (preg_match('/^([-a-zA-Z]+)/', $attr, $match)) {
			  $attrname = strtolower($match[1]);
			  $skip = ($attrname == 'style' || substr($attrname, 0, 2) == 'on');
			  $working = $mode = 1;
			  $attr = preg_replace('/^[-a-zA-Z]+/', '', $attr);
			}
	
			break;
	
		  case 1:
			// Equals sign or valueless ("selected")
			if (preg_match('/^\s*=\s*/', $attr)) {
			  $working = 1;
			  $mode = 2;
			  $attr = preg_replace('/^\s*=\s*/', '', $attr);
			  break;
			}
	
			if (preg_match('/^\s+/', $attr)) {
			  $working = 1;
			  $mode = 0;
			  if (!$skip) {
				$attrarr[] = $attrname;
			  }
			  $attr = preg_replace('/^\s+/', '', $attr);
			}
	
			break;
	
		  case 2:
			// Attribute value, a URL after href= for instance
			if (preg_match('/^"([^"]*)"(\s+|$)/', $attr, $match)) {
			  $thisval = $this->filter_xss_bad_protocol($match[1]);
	
			  if (!$skip) {
				$attrarr[] = "$attrname=\"$thisval\"";
			  }
			  $working = 1;
			  $mode = 0;
			  $attr = preg_replace('/^"[^"]*"(\s+|$)/', '', $attr);
			  break;
			}
	
			if (preg_match("/^'([^']*)'(\s+|$)/", $attr, $match)) {
			  $thisval = $this->filter_xss_bad_protocol($match[1]);
	
			  if (!$skip) {
				$attrarr[] = "$attrname='$thisval'";
			  }
			  $working = 1;
			  $mode = 0;
			  $attr = preg_replace("/^'[^']*'(\s+|$)/", '', $attr);
			  break;
			}
	
			if (preg_match("%^([^\s\"']+)(\s+|$)%", $attr, $match)) {
			  $thisval = $this->filter_xss_bad_protocol($match[1]);
	
			  if (!$skip) {
				$attrarr[] = "$attrname=\"$thisval\"";
			  }
			  $working = 1;
			  $mode = 0;
			  $attr = preg_replace("%^[^\s\"']+(\s+|$)%", '', $attr);
			}
	
			break;
		}
	
		if ($working == 0) {
		  // not well formed, remove and try again
		  $attr = preg_replace('/
			^
			(
			"[^"]*("|$)     # - a string that starts with a double quote, up until the next double quote or the end of the string
			|               # or
			\'[^\']*(\'|$)| # - a string that starts with a quote, up until the next quote or the end of the string
			|               # or
			\S              # - a non-whitespace character
			)*              # any number of the above three
			\s*             # any number of whitespaces
			/x', '', $attr);
		  $mode = 0;
		}
	  }
	
	  // the attribute list ends with a valueless attribute like "selected"
	  if ($mode == 1) {
		$attrarr[] = $attrname;
	  }
	  return $attrarr;
	}
	
	
	
	public function decode_entities($text, $exclude = array()) {
	  static $html_entities;
	  if (!isset($html_entities)) {
		//include_once './includes/unicode.entities.inc';
	  }
	
	  // Flip the exclude list so that we can do quick lookups later.
	  $exclude = array_flip($exclude);
	
	  // Use a regexp to select all entities in one pass, to avoid decoding 
	  // double-escaped entities twice. The PREG_REPLACE_EVAL modifier 'e' is
	  // being used to allow for a callback (see 
	  // http://php.net/manual/en/reference.pcre.pattern.modifiers).
	  return preg_replace('/&(#x?)?([A-Za-z0-9]+);/e', '$this->_decode_entities("$1", "$2", "$0", $html_entities, $exclude)', $text);
	}
	
	
	
	public function filter_xss_bad_protocol($string, $decode = TRUE) {
	  static $allowed_protocols;
	  if (!isset($allowed_protocols)) {
		$allowed_protocols = array_flip(array('http', 'https'));
	  }
	  // Get the plain text representation of the attribute value (i.e. its meaning).
	  if ($decode) {
		$string = $this->decode_entities($string);
	  }
	
	  // Iteratively remove any invalid protocol found.
	
	  do {
		$before = $string;
		$colonpos = strpos($string, ':');
		if ($colonpos > 0) {
		  // We found a colon, possibly a protocol. Verify.
		  $protocol = substr($string, 0, $colonpos);
		  // If a colon is preceded by a slash, question mark or hash, it cannot
		  // possibly be part of the URL scheme. This must be a relative URL,
		  // which inherits the (safe) protocol of the base document.
		  if (preg_match('![/?#]!', $protocol)) {
			break;
		  }
		  // Per RFC2616, section 3.2.3 (URI Comparison) scheme comparison must be case-insensitive
		  // Check if this is a disallowed protocol.
		  if (!isset($allowed_protocols[strtolower($protocol)])) {
			$string = substr($string, $colonpos + 1);
		  }
		}
	  } while ($before != $string);
	  return $string;
	}
	
	
	
	public function _decode_entities($prefix, $codepoint, $original, &$html_entities, &$exclude) {
	  // Named entity
	  if (!$prefix) {
		// A named entity not in the exclude list.
		if (isset($html_entities[$original]) && !isset($exclude[$html_entities[$original]])) {
		  return $html_entities[$original];
		}
		else {
		  return $original;
		}
	  }
	  // Hexadecimal numerical entity
	  if ($prefix == '#x') {
		$codepoint = base_convert($codepoint, 16, 10);
	  }
	  // Decimal numerical entity (strip leading zeros to avoid PHP octal notation)
	  else {
		$codepoint = preg_replace('/^0+/', '', $codepoint);
	  }
	  // Encode codepoint as UTF-8 bytes
	  if ($codepoint < 0x80) {
		$str = chr($codepoint);
	  }
	  else if ($codepoint < 0x800) {
		$str = chr(0xC0 | ($codepoint >> 6))
			 . chr(0x80 | ($codepoint & 0x3F));
	  }
	  else if ($codepoint < 0x10000) {
		$str = chr(0xE0 | ( $codepoint >> 12))
			 . chr(0x80 | (($codepoint >> 6) & 0x3F))
			 . chr(0x80 | ( $codepoint       & 0x3F));
	  }
	  else if ($codepoint < 0x200000) {
		$str = chr(0xF0 | ( $codepoint >> 18))
			 . chr(0x80 | (($codepoint >> 12) & 0x3F))
			 . chr(0x80 | (($codepoint >> 6)  & 0x3F))
			 . chr(0x80 | ( $codepoint        & 0x3F));
	  }
	  // Check for excluded characters
	  if (isset($exclude[$str])) {
		return $original;
	  }
	  else {
		return $str;
	  }
	}	

	
	
	
	////////////////////////////////////////////////////////////////////////////
	
	public function isInteger($val) {
		
		/* if(is_int($i)) // or is_integer($val)
		{
			return true;
		}
		else
		{
			return false;
		} */
		
		return (bool)preg_match("/^[0-9]+$/i", $val);
		
	}
	
	
	
	/**
	* check a number optional -,+,. values
	* @param   string        
	* @return  boolean
	*/
	
	public function isNumeric($val)
	{
	  return (bool)preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $val);
	  
	  //is_numeric � Finds whether a variable is a number or a numeric string 
	  
	  // return is_numeric($val);
	  /*
		'42' is numeric
		'1337' is numeric
		'1e4' is numeric
		'not numeric' is NOT numeric
		'Array' is NOT numeric
		'9.1' is numeric
	
	  */
	 
	}
	
	public function isDecimal($val)
	{
		// check decimal with . is optional and after decimal places up to 6th precision
		return (bool)preg_match("/^\d+(\.\d{1,6})?$/'", $val);
	
	}
	

	
	public function isAlphabetic($val)
	{
		/* if(ctype_alpha($val))
		{
			return true;
		}
		else
		{
			return false;
		} */
		
		return (bool)preg_match("/^([a-zA-Z])+$/i", $val);
	
	}
	
	
	public function isValidHindiText($val){
		
		return (bool)preg_match("/^([\x{900}-\x{97F}\ ])+$/ui", $val);

	}
	
	
	/**
	 * Matches alpha and dashes like -,_
	 * @param   string  
	 * @return  boolean
	 */
	public function isAlphaDash($val)
	{
		return (bool)preg_match("/^([A-Za-z_-])+$/i", $val);
	
	}
	
	
	
	public function isAlphaNumeric($val)
	{
		/* if(ctype_alnum($val))
		{
			return true;
		}
		else
		{
			return false;
		} */
		
		
		return (bool)preg_match("/^([a-zA-Z0-9])+$/i", $val);
	
	}
	
	
	
	public function isValidSortField($val)
	{
		
		
		if(strstr($val,';'))
		{
			return false;
		}
		
		if(strstr($val,':'))
		{
			return false;
		}

		
		return (bool)preg_match("/^([a-zA-Z0-9.-_])+$/i", $val);
		
		
	
	}
	

	public function isAlphabeticWhiteSpace($val)
	{
	
		if (preg_match('/^[A-Za-z\s*]+$/i', $val)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	
	
	public function isAlphaDashWhiteSpace($val)
	{
		return (bool)preg_match("/^([A-Za-z_-\s*])+$/i", $val);
	
	}
	
	
	
	public function isAlphaNumericWhiteSpace($val)
	{
	
		if (preg_match('/^[A-Za-z0-9\s*]+$/i', $val)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	
	public function isAlphabeticSpace($val)
	{
	
		if (preg_match('/^[A-Za-z ]+$/i', $val)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	public function isAlphaDashSpace($val)
	{
		return (bool)preg_match("/^([A-Za-z\- ])+$/i", $val);
	
	}

	public function isAlphaDashSpaceDot($val)
	{
		return (bool)preg_match("/^([A-Za-z\. ])+$/i", $val);
	
	}

	public function isAlphaNumericSpace($val)
	{
	
		if (preg_match('/^[A-Za-z0-9 ]+$/i', $val)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	public function isAlphaNumericSpaceAndSymbols($val)
	{
		//echo $val;
		if (preg_match('/^[A-Za-z0-9 \,\/\(\)\-\r\n]+$/i', $val)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	
	
	
	public function isEmail($val)
	{
	  return (bool)(preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix",$val));
	  
	 // return (bool)preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $val);
	 
	}
	
	
	
	public function isURL($val)
    {
        return (bool)preg_match("/^((((https?|ftps?|gopher|telnet|nntp):\/\/)|(mailto:|news:))(%[0-9A-Fa-f]{2}|[-()_.!~*';\/?:@&=+$,A-Za-z0-9])+)([).!';\/?:,][[:blank:]])?$/i",$val);
 
    }
	public function isURLWithoutProtocol($val)
    {
		/*return (bool)preg_match("/^((%[0-9A-Fa-f]{2}|[-()_.!~*';\/?:@&=+$,A-Za-z0-9])+)([).!';\/?:,][[:blank:]])?$/i",$val);
 */
 
 return (bool) preg_match("/^[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/i", $val);
    }
	
	public function isIPAddress($val)
	{
		  return (bool)preg_match("/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/",$val);
	 
	}
	
	
	public function isValidVideoURL($id)
	{
	 return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $id);
	}  
	
	public function isValidPlayURL($id)
	{
	 return preg_match('|^rtmp?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $id);
	}
	
	
	
	
	function isHTTPOnlyURL($val)
	{
			return (bool) preg_match("/^http:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/i", $val);
	
	}
	
	  /**
	  * Check if given string matches any format date
	  * @param   string   
	  * @return  boolean
	  */
	public function isDate($val)
	{
		return (strtotime($val) !== false);
	
	}
	
	public function isDateISO($date)
	{
		return (bool)preg_match("/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/", $date);
	}
	
	
	public function isDateMySQL($date)
	{
		return (bool)preg_match("/^\d{4}[-]\d{1,2}[-]\d{1,2}$/", $date);
	}
	
	  /**
	  * Checks given value matches date de
	  * @param   string         
	  * @return  boolean
	  */
	 
	public function isDateDE($date)
	{
		// Validation for dateDe [d.m.y]
		return (bool)preg_match("/^\d\d?\.\d\d?\.\d\d\d?\d?$/", $date);
	}
	
	
	
	// validation for // dd/mm/yyyy
	
	
	public function isDateDDMMYYYYSlash($date)
	{
		
		return (bool)preg_match("/^\d{1,2}\/\d{1,2}\/\d{4}$/", $date);
	}
	
	
	  /**
	  * Valid Credit Card
	  * @param   string   
	  * @return  boolean
	  */
	  
	public function isCreditCard($val)
	{
	  return (bool)preg_match("/^((4\d{3})|(5[1-5]\d{2})|(6011)|(7\d{3}))-?\d{4}-?\d{4}-?\d{4}|3[4,7]\d{13}$/", $val);
	 
	}
	
	public function isSafeHTML($val)
	{
	  return (bool)(!preg_match("/<(.*)>.*<$1>/", $val));
	 
	}
	
	
	
	/**
	* Matches base64 enoding string
	* @param   string   
	* @return  boolean
	*/
	public function isBbase64($val)
	{
		return (bool)!preg_match('/[^a-zA-Z0-9\/\+=]/', $val);
	
	}
		
	 /**
	 * check given string length is between given range 
	 * @param   string   
	 * @return  boolean
	 */
	public function isRangeLength($val, $min = '', $max = '')
	{
		return (strlen($val) >= $min and strlen($val) <= $max);
	}
	
	
	/**
	 * check given number between given values
	 * @param   string   
	 * @return  boolean
	 */
	public function isRangeValue($number,$min,$max)
	{
		return ($number > $min and $number < $max);
	
	}
	
	
	  /**
	 * Time in 12 hours format with optional seconds
	 * 08:00AM | 10:00am | 7:00pm
	 * @param   string         
	 * @return  boolean
	 */
	public function isTime12($val)
	{
		return (bool)preg_match("/^([1-9]|1[0-2]|0[1-9]){1}(:[0-5][0-9][aApP][mM]){1}$/",$val);
	}
	
	/**
	 * Time in 24 hours format with optional seconds
	 * 12:15 | 10:26:59 | 22:01:15 
	 * @param   string         
	 * @return  boolean
	 */
	
	public function isTime24($val)
	{
		return (bool)preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/",$val);
	}
	
	
	
	/**
	 * A token that don't have any white space
	 * @param   string   
	 * @return  boolean
	 */
	public function isToken($val)
	{
		return (bool)!preg_match('/\s/', $val);
	
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     
    public function check_chars($text)
	{
	 	return (preg_match("/^[a-zA-Z ]+$/i", $text));
	}
    
    
    public function isValidEmailFormat($email)
	{
	   if(!@preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) 
			return false;
	   else
			return true;
	}

	

	

	public function chkemp ($str)
	{
		
		if (is_null($str) || trim($str)=="")
		{
			return 2;
		}
		return 1;
	}
	
	public function chkint ($str)
	{
		
		$goodchar = "1234567890";
		
		for ($i=0;$i<strlen($str);$i++)
		    {
			if (strpos($goodchar,$str[$i]) === false)
			   {
				return 2;
			     }
		      }
		return 1;
		
	}
	
	
	
	public function chktelephone ($str)
	{
		
		if ($str[0] != '+')
		{
			return 2;
		}
		
		if ((strpos($str,'-') === false) || (strpos($str,'-') <= 1))
		{
			return 2;
		}
		
		if (is_nan(substr($str, 1, (strpos($str,'.') - 1))) || strlen(substr($str, 1, (strpos($str,'-') - 1))) > 3)
		{
			return 2;
		}
		
		if (is_nan(substr($str, (strpos($str,'-') + 1), ((strlen($str) - 1) - strpos($str,'-')))) || strlen(substr($str, (strpos($str,'-') + 1), ((strlen($str) - 1) - strpos($str,'-')))) > 14 || strlen(substr($str, (strpos($str,'-') + 1), ((strlen($str) - 1) - strpos($str,'-')))) < 1)
		{
			return 2;
		}
		return 1;
		
	}
	
	public function chkchar ($str)
	{
		
		$goodchar = " abcdefghijklmnopqrstuvwxyz";
		
		for ($i=0;$i<strlen($str);$i++)
		{
			if (!($str[$i] == " "))
			{
				if (strpos($goodchar,strtolower($str[$i])) === false)
				{
					return 2;
				}
			}
		}
		return 1;
		
	}
	
	public function chkalphanum ($str)
	{
		
		$goodchar = " abcdefghijklmnopqrstuvwxyz0123456789.";
		
		for ($i=0;$i<strlen($str);$i++)
		{
			if (!($str[$i] == " "))
			{
				if (strpos($goodchar,strtolower($str[$i])) === false)
				{
					return 2;
				}
			}
		}
		return 1;
		
	}
	
	
	
	public function chkmail ($str)
	{
		
		if (is_null($str) || trim($str)=="")
		{
			return 2;
		}
		
		if ((strpos($str,'@') === false) || (strpos($str,'.') === false))
		{
			return 2;
		}
		
		if ((strpos($str,'..') !== false) || (strpos($str,'.@') !== false) || (strpos($str,'@.') !== false))
		{
			return 2;
		}
		
		if ((strpos($str,'.') == 0) || (strpos($str,'@') == 0) || (strpos($str,'.',(strlen($str)-1)) == (strlen($str)-1)) || (strpos($str,'@',(strlen($str)-1)) == (strlen($str)-1)))
		{
			return 2;
		}
		
		return 1;
		
	}
	



	
	

	public function chkpasswd($str)
	{
		if(strlen($str) < 6 || strlen($str) > 10)
		{
			echo"Not a Valid User ID. UserID must contain 6 to 20 characters. Please Re-Enter";
			   return 2;
		}
		$alphabets="abcdefghijklmnopqrstuvwxyz0123456789-+_.?@$%/\&*,;:'(){}[]`~!^| ";
		
		for($i=0;$i< strlen($str);$i++)
		{	
			 if (!($str[$i] == " "))
			{
				if (strpos($alphabets,strtolower($str[$i])) === false)
				{
					return 2;
				}
			}
			
		}
		return 1;
	}
		
	public function chkverifycode($str)
	{
		$alphabets="abcdefghijklmnopqrstuvwxyz0123456789";
		
		if(strlen($str) < 6)
		{ 
	  		echo "Not a Valid Verification Code. Please enter the code shown in the image";
		  return 2;
		 }
	
	 	for($i=0;$i< strlen($str);$i++)
		 {	
				 if (!($str[$i] == " "))
				{
					if (strpos($alphabets,strtolower($str[$i])) === false)
					{
						return 2;
					}
				}
				
		}
		return 1;
	}

	//registration number
	public function chkregnnum($str)
	{
		$alphabets="abcdefghijklmnopqrstuvwxyz0123456789-+_.@$%/\&*,;:'() ";
		for($i=0;$i< strlen($str);$i++)
			{	
				 if (!($str[$i] == " "))
				{
					if (strpos($alphabets,strtolower($str[$i])) === false)
					{
						return 2;
					}
				}
				
			}
		return 1;
	}

	//email 
	


	public function is_number($number)
	{ 
		$text = (string)$number;
		$textlen = strlen($text);
		if ($textlen==0)
		 return 0;
		for ($i=0;$i < $textlen;$i++)
		{ $ch = ord($text[$i]);
		   if (($ch<48) || ($ch>57)) return 0;
		}
		return 1;
	}






 

	public function check($strdate)
	{
		
		//$strdate="99-44-2008";
		
		//Check the length of the entered Date value 
		if((strlen($strdate)<10)OR(strlen($strdate)>10))
		{
			//echo("Enter the date in 'dd//mm/yyyy' format");
			return 2;
		}
		else
		{
		   substr_count($strdate,"-");
		//The entered value is checked for proper Date format 
			if((substr_count($strdate,"-"))<>2)
			{
				//echo("Enter the date in 'dd/mm/yyyy' format");
				return 2;
			}
			else
			{
				$pos=strpos($strdate,"-");
				$date=substr($strdate,0,($pos));
				$result=preg_match("/^[0-9]+$/",$date,$trashed);
				if(!($result))
				{
					//echo "Enter a Valid Date";
					return 2;
				}
				else
				{
				  if(($date<=0)OR($date>31))
				  {
					 //echo "Enter a Valid Date";
					 return 2;
				  }
				}
				$month=substr($strdate,($pos+1),($pos));
				if(($month<=0)OR($month>12))
				{
					//echo "Enter a Valid Month";
					return 2;
				}
				else
				{
					$result=preg_match("/^[0-9]+$/",$month,$trashed);
					if(!($result))
					{
						//echo "Enter a Valid Month";
						return 2;
					}
				}
				$year=substr($strdate,($pos+4),strlen($strdate));
				$result=preg_match("/^[0-9]+$/",$year,$trashed);
				if(!($result))
				{
					//echo "Enter a Valid year";
					return 2;
				}
				else
				{
					if(($year<1900)OR($year>2200))
					{
						//echo "Enter a year between 1900-2200";
						return 2;
					}
				}
				// check for leap year if the month and day is Feb 29
				if (($month == 2) && ($date == 29)) 
				{
					 $div4 = $year % 4;
					 $div100 = $year % 100;
					 $div400 = $year % 400;
					
					// if not divisible by 4, then not a leap year so Feb 29 is invalid
					if ($div4 != 0) 
					{
						//alert ("Enter a valid date in DD-MM-YYYY format, Invalid Date"); 
						return 2; 
					}
					
					// at this point, year is divisible by 4. So if year is divisible by
					// 100 and not 400, then it's not a leap year so Feb 29 is invalid
					if (($div100 == 0) && ($div400 != 0)) 
					{
					//	alert ("Enter a valid date in DD-MM-YYYY format, Invalid Date"); 
						return 2; 
					}
				}
			}
		  // return 1;
		}
	   DisplayForm();
	}

	//User-defined public function to display the form in case of Error
	public function DisplayForm()
	{
		global $strdate;
	
	}


	public function chknum($str)
	{
		
		$goodchar = "0123456789.";
		
		for ($i=0;$i<strlen($str);$i++)
		{
			if (!($str[$i] == " "))
			{
				if (strpos($goodchar,strtolower($str[$i])) === false)
				{
					return 2;
				}
			}
		}
		return 1;
		
	}
	





	
	public function chkInjection($str)
	{	
		global $i;
		$regexparr = array(	"/\\w*((\\%27)|(\'))\\s*(((\\%45)|(\\-))((\\%45)|(\\-))|((\\#)|(\\%35)))\\s*/i",
								"/\\w*((\\%27)|(\'))\\s*((\\%6F)|o|(\\%4F))((\\%72)|r|(\\%52))/i",
								"/\\w*((\\%27)|(\'))\\s*((\\%47)|(\\/))((\\%42)|(\\*))\\s*/i",
								"/\\w*((\\%27)|(\'))\\s*\\;\\s*(delete|drop|insert|select|truncate)*\\s*/i",
								"/\\w*((\\%27)|(\'))union\\s*/",
								"/\\w*((\\%27)|(\'))\\s*union\\s+(select|delete|drop|insert)\\s+(((\\%42)|(\\*))|from|table|into)/i",
								"/\\w*((\\%27)|(\'))\\s*((having|group|order)|((group|order)\\s+by))\\s*/i",
								"/\\w*union\\s*(delete|drop|insert|select|truncate)\\s*/i",
								"/\\w*\\;\\s*(delete|drop|insert|select|truncate)\\s*/i",
								"/exec(\\s|\\+)+(((s|x)p)|(master.dbo.xp))\\w+/i",
								"/\\w*select\\s*(\\*+|\\w+)\\s+(from)\\s+(\\w*)/i",
								"/(\\w)*((\\%47)|(\\/))((\\%42)|(\\*)).*((\\%42)|(\\*))((\\%47)|(\\/))\\w*/i",
								"/\\w*(and|or)\\s*((((\\%27)|(\')).?=.?((\\%27)|(\'))*)|(.?=.?))/i",
								"/(begin)\\s+(declare)\\w*/i"
							); 
	
		while($i<count($regexparr))
		{
			if (preg_match($regexparr[$i], $str, $matches)) 
			{
    		return 2;
		
			} 
			$i++;
	  	}// End of While
	  return 1;
	}	
	
	

	
	function isValidFilePath ($filePath)
	{
		$badch=array("../",'./');
	
		if (!(is_null($filePath)) || trim($filePath)!="")
		{
			for ($i=0;$i<count($badch);$i++)
			{
				if (strpos(strtoupper($filePath),strtoupper($badch[$i])) !== false)
				{
					return false;
				}
			}
		}
		return true;
	
	}	

	
	
}
