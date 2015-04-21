<html>

<body>
<h1>This section allows you to add new keywords to the synonyms database</h1><br>

<h2>(Type in words via the following format: "dell;inspiron;xps;")</h2><br/><br/>
<form action = "keywords.php" method = "post">

Enter keywords: <input type = "text" name = "keywords"/>&nbsp;&nbsp;

<input type="submit" name="Submit"/><br>

<?php

$connection = new MongoClient();
$db = $connection->keyword;	//Database name:keyword
$collection = $connection->keyword->aCollection;	//collection name: aCollection

if ( isset( $_POST["keywords"] ) ) 
{ 
	$words = $_POST["keywords"];

	$KWords = explode(";", $words);		//break down into individual words

	echo "<br/>You Entered:<br/>";

	foreach ($KWords as $key) 
	{
		echo $key;
		echo "<br/>\n";
		
	}

	$collection->insert($KWords);
	echo "<br/>\n";
	echo "<font size=6>Successfully inserted into the database</font>";
	//parse_str($words,$array);
	//print_r($array);


}



?>

</form>

</body>
</html>