<html>
<!--
scale for Followers is stored in 'favscale'db
Retweets in 'rtscale' db
Mentions in 'mentionscale' db

-->
<head>

<link rel="stylesheet" href="ahpstyle.css" type="text/css" media="all">
<script type="text/javascript" 
   src="jquery-1.11.1.js"></script>
   <script type="text/javascript" src="ahpcustom.js">
      // you can add our javascript code here
 </script> 


<div class = "test">
	<a href = "index.php"><img class = "bottom" src = "backhov.png"/>
	<img class = "top" src ="back.png"/></a>
</div>

<h1>Twitter Influence Level calculation via Analytical Hierarchy Process (AHP)</h1>
<h3 style = "color:white;">Search will return top five users that most active with the inputed query. These five users will then be ranked according to AHP.</h3>

<form action = "ahp.php" method = "post">

Enter keyword: <input type = "text" name = "keyword"/>
&nbsp;
<input type="submit" name="Submit" value = "Submit"/><br>

Number of users to compare: 
	<select name = "userreturncount">
	  <option value="3">3</option>
	  <option value="4">4</option>
	  <option value="5">5</option>
	  <option value="6">6</option>
	  <option value="7">7</option>
	  <option value="8">8</option>
	  <option value="9">9</option>
	  <option value="10">10</option>
	</select>

</head>

<body>
<br><br>

<?php
	
	require 'edelman2.php';
	$connection = new MongoClient(); // connects to localhost:27017
	//I changed "twitterdb" to "twitterdb2"
	$db = $connection->twitterdb2;
	//echo "Database connected! <br/>\n";
	$collection = $connection->twitterdb2->scale;

	$x = 1;
	$maxID;
	$maxID2;
	$arrayrow1;
	$arrayrow2;
	$arrayrow3;
	$rowsum1;
	$rowsum2;
	$rowsum3;
	$priRT;
	$priMen;
	$priFols;

	$RTarray;
	$MentArray;
	$FolsArray;


function normalizeVars(&$arrayrow1,&$arrayrow2,&$arrayrow3,&$rowsum)
{
	$col1;
	$col2;
	$col3;

	$rowsum1 = 0;
	$rowsum2 = 0;
	$rowsum3 = 0;

	$x=0;
	$y=0;
	$z=0;

	$col1 = $arrayrow1[0]+$arrayrow2[0]+$arrayrow3[0];
	$arrayrow1[0] = $arrayrow1[0]/$col1;
	$arrayrow2[0] = $arrayrow2[0]/$col1;
	$arrayrow3[0] = $arrayrow3[0]/$col1;

	$col2 = $arrayrow1[1]+$arrayrow2[1]+$arrayrow3[1];
	$arrayrow1[1] = $arrayrow1[1]/$col2;
	$arrayrow2[1] = $arrayrow2[1]/$col2;
	$arrayrow3[1] = $arrayrow3[1]/$col2;

	$col3 = $arrayrow1[2]+$arrayrow2[2]+$arrayrow3[2];
	$arrayrow1[2] = $arrayrow1[2]/$col3;
	$arrayrow2[2] = $arrayrow2[2]/$col3;
	$arrayrow3[2] = $arrayrow3[2]/$col3;

	for ($i=0;$i<3;$i++)
	{
		$x += $arrayrow1[$i];
		$y += $arrayrow2[$i];
		$z += $arrayrow3[$i];
	}

	array_push($rowsum, $x);
	array_push($rowsum, $y);
	array_push($rowsum, $z);
}

function normalizefunc(&$array2,&$rowsum2,&$usercounts)
{
	

	for ($col = 0; $col < $usercounts; $col++)
	{	
		$colsum =0;
		//get base value first
		for($row=0; $row<$usercounts; $row++)
		{
			$colsum += $array2[$row][$col];
		}

		//use base value to replace current values
		for($row=0; $row<$usercounts; $row++)
		{
			$array2[$row][$col] = $array2[$row][$col]/$colsum;
		}
	}

	for ($row=0; $row<$usercounts; $row++)
	{	
		$rowsum = 0;
		for ($col = 0; $col < $usercounts; $col++)
		{
			$rowsum += $array2[$row][$col];
		}

		array_push($rowsum2, $rowsum);
	}
}

function getScaleFols($x,$y,&$connection)
{	
	$scalearray = [];
	$connection = new MongoClient();
	$db = $connection->favscale;	//Database name:keyword
	$collection = $connection->favscale->Collection;	//collection name: aCollection
	$cursor = $collection->find();

	foreach ($cursor as $key => $value) 
	{
		//echo $value[0];
		array_push($scalearray, $value[0]);
	}

	$scale;
	$inverse = False;

	if ($y == 0)
	{$y = 1;}

	$z = $x/$y;

	if ($z < 1)
		{	
			$inverse = True;	
			$z = 1 / $z;
		}

	//echo "$z<br>";
	switch ($z) {
			case $z <= $scalearray[0]:
				$scale = 1;
				break;

			case $z < $scalearray[1]:
				$scale = 2;
				break;

			case $z < $scalearray[2]:
				$scale = 3;
				break;

			case $z < $scalearray[3]:
				$scale = 4;
				break;

			case $z < $scalearray[4]:
				$scale = 5;
				break;

			case $z < $scalearray[5]:
				$scale = 6;
				break;

			case $z < $scalearray[6]:
				$scale = 7;
				break;

			case $z < $scalearray[7]:
				$scale = 8;
				break;

			case $z >= $scalearray[8]:
				$scale = 9;
				break;

			default:
					 echo "Invalid input.";
					 echo "<br>\n";
				break;
		}	

	if ($inverse == True)
	{
		$scale = 1 / $scale;
	}

	return $scale;
}

function getScaleRT($x,$y)
{	
	$scalearray = [];
	$connection = new MongoClient();
	$db = $connection->rtscale;	//Database name:keyword
	$collection = $connection->rtscale->Collection;	//collection name: aCollection
	$cursor = $collection->find();

	foreach ($cursor as $key => $value) 
	{
		//echo $value[0];
		array_push($scalearray, $value[0]);
	}

	$scale;
	$inverse = False;

	if ($y==0)
	{	$y = 1;	}
	
	if ($x==0)
	{	$x = 1;	}
	$z = $x/$y;

	if ($z < 1)
		{	
			$inverse = True;	
			$z = 1 / $z;
		}

	//echo "$z<br>";
	switch ($z) {
			case $z <= $scalearray[0]:
				$scale = 1;
				break;

			case $z < $scalearray[1]:
				$scale = 2;
				break;

			case $z < $scalearray[2]:
				$scale = 3;
				break;

			case $z < $scalearray[3]:
				$scale = 4;
				break;

			case $z < $scalearray[4]:
				$scale = 5;
				break;

			case $z < $scalearray[5]:
				$scale = 6;
				break;

			case $z < $scalearray[6]:
				$scale = 7;
				break;

			case $z < $scalearray[7]:
				$scale = 8;
				break;

			case $z >= $scalearray[8]:
				$scale = 9;
				break;

			default:
					 echo "Invalid input.";
					 echo "<br>\n";
				break;
		}	

	if ($inverse == True)
	{
		$scale = 1 / $scale;
	}

	return $scale;
}

function getScaleMen($x,$y)
{	
	$scalearray = [];
	$connection = new MongoClient();
	$db = $connection->mentionscale;	//Database name:keyword
	$collection = $connection->mentionscale->Collection;	//collection name: aCollection
	$cursor = $collection->find();

	foreach ($cursor as $key => $value) 
	{
		//echo $value[0];
		array_push($scalearray, $value[0]);
	}

	$scale;
	$inverse = False;

	if ($y == 0)
	{	$y = 1;	}

		$z = $x/$y;

	if ($z < 1)
		{	
			$inverse = True;	
			$z = 1 / $z;
		}

		//echo "$z<br>";
		switch ($z) {
				case $z <= $scalearray[0]:
					$scale = 1;
					break;

				case $z < $scalearray[1]:
					$scale = 2;
					break;

				case $z < $scalearray[2]:
					$scale = 3;
					break;

				case $z < $scalearray[3]:
					$scale = 4;
					break;

				case $z < $scalearray[4]:
					$scale = 5;
					break;

				case $z < $scalearray[5]:
					$scale = 6;
					break;

				case $z < $scalearray[6]:
					$scale = 7;
					break;

				case $z < $scalearray[7]:
					$scale = 8;
					break;

				case $z >= $scalearray[8]:
					$scale = 9;
					break;

				default:
						 echo "Invalid input.";
						 echo "<br>\n";
					break;
			}	

		if ($inverse == True)
		{
			$scale = 1 / $scale;
		}

		return $scale;

}

function calculateAHP(&$connection,&$shortlist,&$rowsum,&$rowsum2,&$UserFolsScore,&$UserRTScore,&$UserMenScore,&$usercounts)
{	
	/*
	foreach ($cursor as $key => $value){
		
		echo json_encode($value);
		echo"<br>\n";
		//array_push($array,$doc);
		//var_dump($doc);	}
	*/

	$db = $connection->twitterusers;
	//echo "Database connected! <br/>\n";
	$collection = $connection->twitterusers->users;
	$cursor = $collection->find();

	$array = [];
	$arrayvalues = [];
	$array2 = [];
	$arrayrow1 = [];
	$arrayrow2 = [];
	$arrayrow3 = [];
	$arrayrow4 = [];
	$arrayrow5 = [];
	

	//echo"Calculate AHP function dumping array<br>\n";
	
	foreach ($cursor as $doc) {
		$temp = array("username" => $doc["Username"],
						"followers" => $doc["Followers"],
						"retweets" => $doc["Retweets"],
						"mentions" => $doc["Mentions"]);
		array_push($array,$temp);
	}

	//var_dump($array);
	echo "<div id='container'>";
	//iterator = 0 for Fols, 1 for RT
	for ($iterator =0; $iterator<3;$iterator++)
	{	
		$array2 = [];
		//echo "-------------------------------------------------------------------<br>\n";
		if ($iterator == 0)
		{	
			switch ($usercounts) 
			{
				case '3':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleFols($array[$row]["followers"],$array[$col]["followers"],$connection);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2]);
						array_push($array2, $doc);
					}

					break;
				
				case '4':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleFols($array[$row]["followers"],$array[$col]["followers"],$connection);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3]);
						array_push($array2, $doc);
					}

					break;

				case '5':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleFols($array[$row]["followers"],$array[$col]["followers"],$connection);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4]);
						array_push($array2, $doc);
					}

					break;

				case '6':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleFols($array[$row]["followers"],$array[$col]["followers"],$connection);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5]);
						array_push($array2, $doc);
					}

					break;

				case '7':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleFols($array[$row]["followers"],$array[$col]["followers"],$connection);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6]);
						array_push($array2, $doc);
					}

					break;

				case '8':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleFols($array[$row]["followers"],$array[$col]["followers"],$connection);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6],$arrayvalues[7]);
						array_push($array2, $doc);
					}


					break;

				case '9':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleFols($array[$row]["followers"],$array[$col]["followers"],$connection);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6],$arrayvalues[7],$arrayvalues[8]);
						array_push($array2, $doc);
					}

					break;

				case '10':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleFols($array[$row]["followers"],$array[$col]["followers"],$connection);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6],$arrayvalues[7],$arrayvalues[8],$arrayvalues[9]);
						array_push($array2, $doc);
					}

					break;
				default:
					echo "Invalid Number of Users to compare<br>";
					break;
			}//close bracket for switch
			?>
			<h2>AHP Results</h2><br>
			
		    	<div class="expandable-panel" id="cp-1">
			        <div class="expandable-panel-heading">
			            <h2>Follwers<span class="icon-close-open"></span></h2>
			         </div>
	        	<div class="expandable-panel-content">
			        <table class = "normaltable">
						<?php
						echo "Decision Matrix for Pairwise Comparison:<br>";
						for ($x = 0; $x<$usercounts; $x++)
						{	?>

							<tr>
								<td>
							<?php
								echo "$shortlist[$x]</td>";

							for($y = 0; $y<$usercounts; $y++)
							{	
								?>
									<td>
										<?php
								echo round($array2[$x][$y],3);
								echo "</td>";

							}

							echo "</tr>";
						}
						echo "</table>";
		}

		elseif ($iterator == 1) 
		{
			switch ($usercounts) 
			{
				case '3':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleRT($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2]);
						array_push($array2, $doc);
					}

					break;
				
				case '4':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleRT($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3]);
						array_push($array2, $doc);
					}

					break;

				case '5':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleRT($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4]);
						array_push($array2, $doc);
					}

					break;

				case '6':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleRT($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5]);
						array_push($array2, $doc);
					}

					break;

				case '7':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleRT($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6]);
						array_push($array2, $doc);
					}

					break;

				case '8':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleRT($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6],$arrayvalues[7]);
						array_push($array2, $doc);
					}

					break;

				case '9':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleRT($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6],$arrayvalues[7],$arrayvalues[8]);
						array_push($array2, $doc);
					}
					break;

				case '10':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleRT($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6],$arrayvalues[7],$arrayvalues[8],$arrayvalues[9]);
						array_push($array2, $doc);
					}

					break;
				default:
					echo "Invalid Number of Users to compare<br>";
					break;
			}

			?>

			<div class="expandable-panel" id="cp-2">
		        <div class="expandable-panel-heading">
		            <h2>Retweets<span class="icon-close-open"></span></h2>
		         </div>
		        <div class="expandable-panel-content">
					<table  class = "normaltable">

				<?php
				echo "Decision Matrix for Pairwise Comparison<br>";
				for ($x = 0; $x<$usercounts; $x++)
				{	?>

					<tr>
						<td>
					<?php
						echo "$shortlist[$x]</td>";

					for($y = 0; $y<$usercounts; $y++)
					{	
						?>
							<td>
								<?php
						echo round($array2[$x][$y],3);

					}

					echo "</tr>";
				}
				echo "</table>";
			
		}//close bracket for iterator == 1

		elseif ($iterator == 2) 
		{
			switch ($usercounts) 
			{
				case '3':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleMen($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2]);
						array_push($array2, $doc);
					}

					break;
				
				case '4':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleMen($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3]);
						array_push($array2, $doc);
					}

					break;

				case '5':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleMen($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4]);
						array_push($array2, $doc);
					}

					break;

				case '6':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleMen($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5]);
						array_push($array2, $doc);
					}

					break;

				case '7':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleMen($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6]);
						array_push($array2, $doc);
					}

					break;

				case '8':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleMen($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6],$arrayvalues[7]);
						array_push($array2, $doc);
					}

					break;

				case '9':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleMen($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6],$arrayvalues[7],$arrayvalues[8]);
						array_push($array2, $doc);
					}

					break;

				case '10':
					for ($row = 0; $row < $usercounts; $row++)
					{	
						$arrayvalues = [];
						for ($col = 0; $col <$usercounts; $col++)
						{
							$A = getScaleMen($array[$row]["followers"],$array[$col]["followers"]);
							array_push($arrayvalues, $A);
						}
						
						$doc = array($arrayvalues[0],$arrayvalues[1],$arrayvalues[2],$arrayvalues[3],$arrayvalues[4],$arrayvalues[5],$arrayvalues[6],$arrayvalues[7],$arrayvalues[8],$arrayvalues[9]);
						array_push($array2, $doc);
					}

					break;

				default:
					echo "Invalid Number of Users to compare<br>";
					break;
			}
			
			?>

			<div class="expandable-panel" id="cp-3">
			     <div class="expandable-panel-heading">
			         <h2>Mentions<span class="icon-close-open"></span></h2>
			     </div>
			     <div class="expandable-panel-content">
			     <table class = "normaltable">
				<?php
				echo "Decision Matrix for Pairwise Comparison<br>";
				for ($x = 0; $x<$usercounts; $x++)
				{	?>

					<tr>
						<td>
					<?php
						echo "$shortlist[$x]</td>";

					for($y = 0; $y<$usercounts; $y++)
					{	
						?>
							<td>
								<?php

						echo round($array2[$x][$y],3);

					}

					echo "</tr>";
				}
				echo "</table>";
		}//close bracket for iterator == 2

		
		
		$rows = 6; // define number of rows
		$cols = 6;// define number of columns
		 
		
		normalizefunc($array2,$rowsum2,$usercounts);

		//normalize($arrayrow1,$arrayrow2,$arrayrow3,$arrayrow4,$arrayrow5,$rowsum);

		echo "<br>\n";
		echo "Normalized Matrix:<br>";
		?>
		<table class = "normaltable">
		
		<?php

		for ($row = 0; $row < $usercounts; $row++)
		{	
			?><tr>
				<td>
				<?php
					echo $shortlist[$row];

				for ($col = 0; $col <$usercounts; $col++)
				{	
					?>
						<td>
							<?php

					echo round($array2[$row][$col],3);
					echo "</td>";
				}
				echo "</tr>";			
		}
		
		echo "</table>\n";

		echo "<br>\n";
		echo "Row Sums:<br>";
		?>
		<table class = "normaltable">
		<?php

		//var_dump($UserFolsScore);
		for ($i=0;$i<$usercounts;$i++)
		{	
			?>
			<tr><td><?php
			echo $shortlist[$i];

			echo "</td>";

			echo "<td>";
			echo round($rowsum2[$i],3)."</td>";

			if($iterator == 0)
			{	array_push($UserFolsScore, $rowsum2[$i]/$usercounts);		}

			elseif($iterator == 1)
			{	array_push($UserRTScore, $rowsum2[$i]/$usercounts);	}

			elseif ($iterator == 2) 
			{
				array_push($UserMenScore, $rowsum2[$i]/$usercounts);
			}
			
			echo "</tr>";
		}

		echo "</table>";
		?>
		<br>
		</div></div>
		<div style = "clear:both;"></div>
		<?php
	}

	echo "</div>";

}

function getUserDetails(&$settings,&$connection,&$shortlist,&$usercounts)
{	
	$db = $connection->twitterusers;
	//echo "Database connected! <br/>\n";
	$collection = $connection->twitterusers->users;
	$collection->remove();

	ini_set('max_execution_time', 300);

	for ($i = 0; $i <$usercounts; $i++)	//5 for five users
	{
		$word = $shortlist[$i];
		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$getfield = "?screen_name={$word}&count=150";
		$requestMethod = 'GET';

		$twitter = new TwitterAPIExchange($settings);
		$response = $twitter->setGetfield($getfield)
		                    ->buildOauth($url, $requestMethod)
		                   ->performRequest();
		$json_data = $response;
		$json = json_decode($json_data);
		//$arraycount = count($json->status);
		//echo $arraycount;
		////// End user timeline details
		//$data = json_decode($json);

		$username = $json[0]->user->screen_name;
		$folcount = $json[0]->user->followers_count;
		$totalretweets = 0;
		$totalmentions = 0;

		//RETWEETS calculation
		foreach ($json as $item) 
		{
			//echo $item->retweet_count;
			//echo "<br/>\n";
			//echo $json[0]->retweet_count;
			//$totaltweets++;
			$mystring = $item->text;
			$find = 'RT';
			$pos = strpos($mystring, $find);

			if ($pos !== false)
			{	//echo "Yes\n";
			}
			
			else
			{	//echo "No\n";
					$totalretweets = $totalretweets + $item->retweet_count;
			}
		}

		//USER MENTIONS CALC
		$word = "@" . $word;
		$maxID;
		$maxID2 = "";
		//echo "Username is $word<br/>\n";
		for ($j=0; $j<10; $j++)
		{
			if($j==0)
			{
				$url2 = 'https://api.twitter.com/1.1/search/tweets.json';
				$getfield2 = "?q={$word}&count=50";
				$requestMethod2 = 'GET';

				$twitter = new TwitterAPIExchange($settings);
				$response2 = $twitter->setGetfield($getfield2)
				                    ->buildOauth($url2, $requestMethod2)
				                   ->performRequest();
				$json_data2 = $response2;
				$json2 = json_decode($json_data2);
				$arraycount2 = 0;
				if (empty($json2->statuses))	{	}

				else
				{
					$arraycount2 = count($json2->statuses);

					if ($arraycount2 > 0)
					{
						$totalmentions += $arraycount2;
					}
				}
			}

			else
			{
				$url2 = 'https://api.twitter.com/1.1/search/tweets.json';
				$getfield2 = "?q={$word}&count=51&max_id=$maxID2";
				$requestMethod2 = 'GET';

				$twitter = new TwitterAPIExchange($settings);
				$response2 = $twitter->setGetfield($getfield2)
				                    ->buildOauth($url2, $requestMethod2)
				                   ->performRequest();
				$json_data2 = $response2;
				$json2 = json_decode($json_data2);
				$arraycount2 = 0;
				if (empty($json2->statuses))	{	}

				else
				{
					$arraycount2 = count($json2->statuses);

					if ($arraycount2 > 0)
					{
						$totalmentions += $arraycount2;
					}
				}
			}

			//echo $json2->statuses[$arraycount2-1]->created_at;
			//echo "<br/>\n";

			if ($arraycount2 > 0)
			{
				$maxID2 = $json2->statuses[$arraycount2-1]->id_str;
			}

		}

		//echo "Username: $username<br>\n";
		//echo "Followers Count: $folcount<br>\n";
		//echo "Retweets Count: $totalretweets<br>\n";
		//echo "User mentions Count: $totalmentions<br>\n";
		//echo "<br>\n";
		
		$doc = array( 
					"Username" => $username,
					"Followers" => $folcount,
					"Retweets" => $totalretweets,
					"Mentions" => $totalmentions
				);

		$collection->insert($doc);
		//then add user along with details to mongo!
	}	//end for loop $i


}

function calculateIndex(&$RTPriority,&$MenPriority,&$FolsPriority,&$UserFolsScore,&$UserRTScore,&$UserMenScore,&$Influence,&$shortlist,&$usercounts)
{	
	/*echo "Priority vs Goals for Followers:<br>";
	var_dump($UserFolsScore);
	echo "Priority vs Goals for Retweets:<br>";
	var_dump($UserRTScore);
	echo "Priority vs Goals for Mentions:<br>";
	valuer_dump($UserMenScore);*/

	for ($i = 0; $i <3; $i++)
	{
		if ($i == 0)
		{
			echo "<b>Priority vs Goals for Followers:</b><br>";

			for ($j = 0; $j <$usercounts; $j++)
			{	
				echo "<div style = 'color:orange; display:inline;'>";
				echo $shortlist[$j];
				echo ":</div>";

				echo round($UserFolsScore[$j],3)."<br>";
			}
		}

		elseif ($i == 1)
		{
			echo "<br><b>Priority vs Goals for Retweets:</b><br>";

			for ($j = 0; $j <$usercounts; $j++)
			{
				echo "<div style = 'color:orange; display:inline;'>";
				echo $shortlist[$j];
				echo ":</div>";
				echo round($UserRTScore[$j],3)."<br>";
			}
		}

		elseif ($i == 2)
		{
			echo "<br><b>Priority vs Goals for Mentions:</b><br>";

			for ($j = 0; $j <$usercounts; $j++)
			{
				echo "<div style = 'color:orange; display:inline;'>";
				echo $shortlist[$j];
				echo ":</div>";
				echo round($UserMenScore[$j],3)."<br>";
			}
		}
		
	}

	for ($i = 0; $i <$usercounts; $i++)
	{
		$x = $UserFolsScore[$i]*$FolsPriority;
		$y = $UserRTScore[$i]*$RTPriority;
		$z = $UserMenScore[$i]*$MenPriority;

		$total = $x+$y+$z;

		$doc = array(
						"Username" => $shortlist[$i],
						"Influence" => $total
					);

		array_push($Influence, $doc);
	}

	//var_dump($Influence);
	echo "<br><b>Final AHP Scores:</b><br>";

	$rows = $usercounts+1; // define number of rows
	$cols = 2;// define number of columns
	 
	echo "<table class ='finalahptable'>";
	 
	for($tr=1;$tr<=$rows;$tr++){
	      
	    echo "<tr>";
	        for($td=1;$td<=$cols;$td++)
	        {	
	        	$x = $tr -2;
	        	if ($tr ==1 && $td == 1)
	        	{
	        		echo "<th>Username</th>";
	        	}

	        	elseif ($tr == 1 && $td == 2) 
	        	{
	        		echo "<th>Influence</th>";
	        	}

	        	elseif ($td == 1)
	        	{	
	        		$y = $Influence[$x]["Username"];
	        		echo "<td>$y</td>";
	        	}

	        	elseif ($td == 2)
	        	{
	        		$y = round($Influence[$x]["Influence"], 3);
	        		echo "<td>$y</td>";
	        	}
	        }
	    echo "</tr>";
	}
	 
	echo "</table>"; 


}

if ( isset( $_POST["keyword"] ) ) 
{ 
	$UserFolsScore = [];
	$UserRTScore = [];
	$UserMenScore = [];
	$Influence = [];
	$UserFolsScore = [];
	$ahpandedelarray = [];
	$userlist = [];
	$rowsum = [];
	$rowsum2 = [];
	$word = $_POST["keyword"];
	$AB=0;
	$AC=0;
	$BC=0;
	//number of users to compare
	$usercounts = $_POST['userreturncount'];
	//echo $word;

	//scaling for Followers, RTs and Mentions
	$scalearray = [];
	$connection = new MongoClient();
	$db = $connection->parameterScale;	//Database name:keyword
	$collection = $connection->parameterScale->Collection;	//collection name: aCollection
	$cursor = $collection->find();
	$cnt = 0;
	foreach ($cursor as $doc) 
	{
		//echo $value[0];
		if($cnt ==0)
		{	$AB = $doc["Fol-Rt"];	}

		elseif ($cnt == 1)
		{	$AC = $doc["Fol-Men"];	}

		elseif ($cnt == 2)
		{	$BC = $doc["Rt-Men"];}
		$cnt++;
	}

	/////////////////
	ini_set('max_execution_time', 300);
	require_once('TwitterAPIExchange.php');
	ini_set('display_errors', 1);
	/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
	$settings = array(
	    'oauth_access_token' => "",
	    'oauth_access_token_secret' => "",
	    'consumer_key' => "",
	    'consumer_secret' => ""
	);

	//echo $word;
	$arrayrow1 = array(1, $AB, $AC);
	$arrayrow2 = array(1/$AB, 1, $BC);
	$arrayrow3 = array(1/$AC, 1/$BC, 1);

	normalizeVars($arrayrow1,$arrayrow2,$arrayrow3,$rowsum);
	
	//Important variables
	$RTPriority = round($rowsum[0]/3,3);
	$MenPriority = round($rowsum[1]/3,3);
	$FolsPriority = round($rowsum[2]/3,3);

	$connection = new MongoClient();
	$db = $connection->keyword;	//Database name:keyword
	$collection = $connection->keyword->aCollection;	//collection name: aCollection

	$array = [];
	//array_push($array, $word);
	$cursor = $collection->find();
	//var_dump($cursor);
	
	foreach ($cursor as $key => $value) 	//pick out relevant objects from DB
	{	
		$found = false;
		$max  = count($value)-1;

		for ($i = 0; $i < $max; $i++)
		{	
			if (strtolower($word) == strtolower($value[$i]))
			{
				echo "Synonym match found for $value[$i]!<br>";
				$found = true;
			}
		}

		//echo $value[0];
		//var_dump($key);
		//echo "<br>";

		if ($found == true)
		{	
			echo "<ul>";
			//foreach (($value,1) as $doc) 
			for ($j = 0; $j < $max; $j++)
			{	

				echo "<li>$value[$j]</li>";
				array_push($array, $value[$j]);
			}
			echo "</ul>";
			//echo implode("+OR+", $array);
			$newword = implode("+OR+", $array);
			break;

		}
	}
		
		if ($found != true)
		{
			$newword = $word;
		}
	
	echo "Searching for: $newword<br>";
	//Searching for tweets and storing in Mongo
	
	//$maxID;
	$db = $connection->twitterdb;
	//echo "Database connected! <br/>\n";
	$collection = $connection->twitterdb->tweets;
	$collection->remove();

	for ($i = 0; $i < 10; $i++)
	{
		$x = 1;
		
		if ($i==0)
		{
			$url = 'https://api.twitter.com/1.1/search/tweets.json';
			$getfield = "?q={$newword}&count=100";
			$requestMethod = 'GET';

			$twitter = new TwitterAPIExchange($settings);
			$response = $twitter->setGetfield($getfield)
		                    ->buildOauth($url, $requestMethod)
		                   ->performRequest();
			$json_data = $response;
			$json = json_decode($json_data);
			$arraycount = count($json->statuses);

			//echo "I IS $i and Size is $arraycount<br/>\n";
		}

		else
		{
			//echo "MAX ID is $maxID <br/>\n";

			$url = 'https://api.twitter.com/1.1/search/tweets.json';
			$getfield = "?q={$newword}&count=101&max_id=$maxID";
			$requestMethod = 'GET';

			$twitter = new TwitterAPIExchange($settings);
			$response = $twitter->setGetfield($getfield)
		                    ->buildOauth($url, $requestMethod)
		                   ->performRequest();
			$json_data = $response;
			$json = json_decode($json_data);

			if (empty($json->statuses))	{	}

			else
			{
				$arraycount = count($json->statuses);
			}
			
			//$arraycount = count($json->statuses);

			//echo "I IS $i and Size is $arraycount<br/>\n";
		}

		$bool = False;

		//THIS WORKS, OUTPUTS USERNAMESSS
		if(!empty($json->statuses))
		{
			foreach ($json->statuses as $item) {

				if ($bool == True || $i == 0)
				{
					//echo "Data here!<br>\n";
					//echo $item->user->screen_name ."<br /> \n";
					//echo $item->created_at ."<br /> \n";
					//echo $item->id. "<br /> \n";
					//echo $item->text. "<br /> \n";
					//echo $item->text ."<br /> \n";
					//echo $item->retweet_count ."<br /> \n";

					//echo "<br>\n";
					$doc = array( 
						"Username" => $item->user->screen_name,
						"Text" => $item->text,
						"Retweets" => $item->retweet_count
					);

					array_push($userlist, $item->user->screen_name);
					$collection->insert($doc);
					$x++;
					$maxID = $item->id_str;
					//$maxID = $maxID - 1;
				}

				$bool = True;
			}
		}
	}
	
	echo "Total number of tweets retrieved: ";
	echo count($userlist);
	echo "<br>";

	$countedarray = array_count_values($userlist);
	arsort($countedarray);
	//$countedarray = array_keys($countedarray);

	//var_dump($countedarray);
	$x = 0;
	$shortlist = [];
	
	//push top seven to shortlist
	foreach ($countedarray as $key => $value) {
		//if ($x<$usercounts)
		if ($x<$usercounts)
		{
			array_push($shortlist, $key);
			$x++;
		}
		//echo $value;
		//echo "<br>\n";
	}
	
	//echo "Dump shortlist:<br>";
	//var_dump($shortlist);
?>
	<div id = "blue_box">
		<div id = "left">

	<?php
	getUserDetails($settings,$connection,$shortlist,$usercounts);
	calculateAHP($connection,$shortlist,$rowsum,$rowsum2,$UserFolsScore,$UserRTScore,$UserMenScore,$usercounts);
	calculateIndex($RTPriority,$MenPriority,$FolsPriority,$UserFolsScore,$UserRTScore,$UserMenScore,$Influence,$shortlist,$usercounts);
	?>
		</div>

		<div class = "divider"></div>
		
		<div id = "right">

		<h2>Edelman Results</h2>

			<?php
				$edelman = [];
				$edelsum = 0;
				printnames($settings,$shortlist,$usercounts,$edelman);
				$edelsum = array_sum($edelman);
			?>
		</div>
	</div>
</body>

	<?php

	for ($j=0;$j<$usercounts;$j++)
	{	
		$y = round($Influence[$j]["Influence"], 3);
		$x = round(($edelman[$j]/$edelsum), 3);

		$doc = array("username" => $shortlist[$j],
						"ahp" => $y,
						"edelman" => $x);
		
		array_push($ahpandedelarray,$doc);
	}

	echo "<br><strong>Comparisons between AHP and Edelman Scores:</strong><br><br>";

	$rows = $usercounts+1; // define number of rows
	$cols = 3;// define number of columns
	 
	echo "<table class ='comparisontable'>";
	 
	for($tr=1;$tr<=$rows;$tr++)
		{  
	    echo "<tr>";
	        for($td=1;$td<=$cols;$td++)
	        {	
	        	$x = $tr -2;
	        	if ($tr ==1 && $td == 1)
	        	{
	        		echo "<th>Username</th>";
	        	}

	        	elseif ($tr == 1 && $td == 2) 
	        	{
	        		echo "<th>AHP</th>";
	        	}

	        	elseif ($tr == 1 && $td == 3)
	        	{
	        		echo "<th>Edelman</th>";
	        	}

	        	elseif ($td == 1)
	        	{	
	        		$y = $ahpandedelarray[$x]["username"];
	        		echo "<td>$y</td>";
	        	}

	        	elseif ($td == 2)
	        	{
	        		$y = $ahpandedelarray[$x]["ahp"];
	        		echo "<td>$y</td>";
	        	}

	        	elseif ($td == 3)
	        	{
	        		$y = $ahpandedelarray[$x]["edelman"];
	        		echo "<td>$y</td>";
	        	}
	        }
	    echo "</tr>";
		}
	 
	echo "</table>"; 

}	//End bracket for submit button!

?>





<h3>To add or remove keywords from the synonyms database, click <a href= "keywords.php">here</a>


</html>
