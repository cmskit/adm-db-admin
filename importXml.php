<?php
/**
* 
* simple Inclusion/Redirection of/to adminer.php
*/
require dirname(__DIR__) . '/header.php';

// 
if (isset($_FILES['userfile']['tmp_name']) && is_uploaded_file($_FILES['userfile']['tmp_name']))
{

	define('SCRIPT_START',microtime(true));

	require $projectPath . '__model.php';

	echo '<!DOCTYPE html>
	<html>
	<head>
	<meta charset="utf-8" />
	<style>body{padding:50px;font:.8em sans-serif;}</style>
	</head>
	<body>
	<p>
	<a href="index.php?project='.$projectName.'">back to Index</a>
	</p>
	';


	// https://forums.digitalpoint.com/threads/import-xml-to-mysql-database-using-php.550665

	$fp = fopen($_FILES['userfile']['tmp_name'], "r") or die("Error reading XML-Data!");
	$tag = '';
	$objname = '';
	$elname = '';
	$type = '';
	$goon = false;

	function startElement($parser, $type, $attrs)
	{
		global $goon, $str, $tag, $projectPath, $obj, $projectName, $objname, $elname;
		
		$tag = $type;
		$elname = $attrs['NAME'];
		
		// create a new Object
		if ($type == "TABLE" && file_exists($projectPath. 'class.' . $attrs['NAME'] . '.php'))
		{
			require_once $projectPath. 'class.' . $attrs['NAME'] . '.php';
			
			// hack considering that we have no namespace in mapping-objects
			//$o = (substr($attrs['NAME'], -3)=='map') ? $attrs['NAME'] : $projectName.'\\'.$attrs['NAME'];
			$o = $projectName.'\\'.$attrs['NAME'];
			$obj = new $o();
			
			echo "new Entry in: <b>" . $attrs['NAME'] . "</b> ( ";
			
			
			$objname = $attrs['NAME'];
			$goon = true;
			$str = '';
		}
		else
		{
			$goon = false;
		}
	}

	function endElement($parser, $type)
	{
		global $goon, $str, $tag, $obj, $objname, $elname, $objects;
		
		if($tag == "COLUMN")
		{
			$str = trim($str);
			echo $elname . ' (' . strlen($str) . '), ';
			
			// special treatment of Model-Fields: transform the string to an array
			/*
			if (
				isset($objects[$objname]['col'][$elname]['type']) 
				&& $objects[$objname]['col'][$elname]['type'] == 'MODEL'
				&& $jarr = json_decode(urldecode($str), true)
			)
			{
				$str = $jarr;
			}*/
			
			// assign content to the field
			if (isset($obj->{$elname}) && strlen($str)>0)
			{
				$obj->{$elname} = $str;
			}
			
			// reset content
			$str = '';
		}
		
		// save to DB
		if (is_object($obj) && $type == "TABLE")
		{
			$obj->Save();
			$obj = null;
			$objname = '';
			echo ")<hr>";
			$goon = false;
		}
	}

	function getData($parser, $data)
	{
		global $goon, $str, $tag;
		
		if($tag == "COLUMN")
		{
			$str .= $data;
		}
	}

	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	xml_set_character_data_handler($xml_parser, "getData");

	while ($data = fread($fp, 10240)) // read chunk
	{
		xml_parse($xml_parser, $data, feof($fp)) 
		or die(
			sprintf(
				"XML-Error: %s at Line %d",
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)
			)
		);
	}
	fclose($fp);
	xml_parser_free($xml_parser);

	$asec = microtime(true) - SCRIPT_START;
	echo '<div>Runtime: ' . number_format($asec, 5, ',', '') . ' sec</div>';

	echo '</body></html>';

} // is_uploaded_file END
else
{ //if no FILE was uploaded, show Upload-Form

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />

<style>
body{
	font: .8em sans-serif;
}
#wrapper {
	position: absolute;
	padding: 10px;
	border: 1px solid #ccc;
	top: 50%;
	left: 50%;
	width: 400px;
	margin: -200px 0 0 -210px;
}
</style>

</head>
<body>

<div id="wrapper">
<a href="index.php?project=<?php echo $projectName?>">back</a>
<h4>Import XML-Dump into your DB</h4>
<p>(Export via Adminer)</p>
<form enctype="multipart/form-data" action="importXml.php?project=<?php echo $projectName?>" method="post" onsubmit="return askUser()">
	<!-- <input type="hidden" name="MAX_FILE_SIZE" value="6000000" />-->
	<input name="userfile" type="file" /><hr />
	<input type="submit" value="import" /> <span id="waiter" title="please wait!"></span>
</form>
</div>
<script>

function askUser()
{
	var q = confirm('do you really want to import this File and (possibly) overwrite Data in your Database?');
	if(q) {
		document.getElementById('waiter').innerHTML = '<img src="../../inc/css/spinner-mini.gif" />';
	}
	return q;
}

</script>
</body>
</html>
<?php

} // else END
?>
