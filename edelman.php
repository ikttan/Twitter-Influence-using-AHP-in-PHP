<html>

<link rel="stylesheet" href="edelmanstyle.css" type="text/css" media="all">

<body>
<!--<div class = "backbtn"></div>-->
<div class = "test">
	<a href = "index.php"><img class = "bottom" src = "backhov.png"/>
	<img class = "top" src ="back.png"/></a>
</div>

<h2>User Influence via Edelman methodology</h2>
<form action = "edelman.php" method = "post">
<br><br/>
<b><br>Search returns a user's influence score based on Edelman's methodology. The influence calculation process is altered to suit the Malaysian context.</b>
<br><br>
Enter username: <input type = "text" name = "keyword"/>
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Submitt" value ="Submit"/><br><br>

<?php
//echo $word;

require_once('TwitterAPIExchange.php');
ini_set('display_errors', 1);
/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "",
    'oauth_access_token_secret' => "",
    'consumer_key' => "",
    'consumer_secret' => ""
);

// declarations

$maxID;
$word;

///Start user timeline details
//$url = 'https://api.twitter.com/1.1/users/show.json';
function test(){echo "HI";}
function printnames(&$settings,&$shortlist,&$usercounts)
{
	?>
	<table border = "1">
		<tr>
	<?php


	for ($i=0;$i<$usercounts;$i++)
	{
		$word = $shortlist[i];

		$totalinf =0;
		$folcount =0;
		$folScore =0;
		$friendscount =0;
		$Ffratio = 0;
		$totaltweets =0;
		$totalretweets = 0;
		$RTScore = 0;
		$totalmentions = 0;
		$totalusermentions = 0;
		$totalusermentionsSc = 0;

		calcRTsandOtherMention($settings,$word,$totalretweets,$totaltweets,$totalusermentions,$RTScore,$totalusermentionsSc,$folcount,$friendscount);
		echo "<td class = 'firstcol'>Username</td><td class = 'secondcol'>$word</td>\n";
		echo "</tr><tr>\n";
		echo "<td class = 'firstcol'>Number of Followers</td><td class = 'secondcol'>$folcount</td></tr><tr>";
		echo "<td class = 'firstcol'>Number of Users Following</td><td class = 'secondcol'>$friendscount</td></tr><tr>";
		//echo "<br/>\n";

		//echo "Number of Followers: $folcount <br>\n";
		$folScore = calculateFols($folcount);
		$folScore = round($folScore,3);
		echo "<td class = 'firstcol'>Followers score</td><td class = 'secondcol'>$folScore</td></tr>\n";
		
		echo "<tr><td class = 'firstcol'>Total retweets</td><td class = 'secondcol'>$totalretweets</td></tr>";
		echo "<tr><td class = 'firstcol'>Total retweets Score</td><td class = 'secondcol'>$RTScore</td></tr>";

		//CHANGE DENOMINATOR IF TWEET SEARCH COUNT IS ALTERED.
		calcMentionsScore($settings,$word,$totalmentions,$totalmentionsSc);
		echo "<tr><td class = 'firstcol'>User mentioned on Twitter</td><td class = 'secondcol'>$totalmentions</td></tr>";
		echo "<tr><td class = 'firstcol'>User mentioned on Twitter Score</td><td class = 'secondcol'>$totalmentionsSc</td></tr>";

		echo "<tr><td class = 'firstcol'>Timeline user mentions</td><td class = 'secondcol'>$totalusermentions</td></tr>\n";
		$totalusermentionsSc = round($totalusermentionsSc,3);
		echo "<tr><td class = 'firstcol'>Timeline user mentions Score</td><td class = 'secondcol'>$totalusermentionsSc</td></tr>\n";

		$Ffratio = calculateFfRatio($friendscount,$folcount);
		$Ffratio = round($Ffratio,3);
		echo "<tr><td class = 'firstcol'>Followers:Following ratio Score</td><td class = 'secondcol'>$Ffratio</td></tr>";
		echo "</table>";
		//$totalretweets = ($totalretweets / 100) * 30;
		//$totalmentions = $totalmentions / 100 * 30;
		$totalinf = $RTScore + $totalmentionsSc + $folScore + $Ffratio + $totalusermentionsSc;
		$finalscore = round($totalinf,3);
		echo "<br>\n<div class = 'score'><font size=10>User Influence = $finalscore </font></div>";

	}

}

//Followers score - 25%
function calculateFols($num)
{	
	//echo "num is $num<br>";
	$fScore = 0;
	
	if ($num < 1000)
	{
		$fScore = ($num / 1000) * 10;
	}

	elseif ($num <= 150000)
	{
		$fScore = log($num,1.99);
		//echo "Matched! Fscore is $fScore<br>";
	}

	elseif ($num <= 150000)
	{
		$num = $num/1000;
		$fScore = log($num, 1.335);
	}

	elseif ($num < 600000) 
	{
		$num = $num/10000;
		$fScore = log($num,1.185);
	}

	else
		{	$fScore = 25;	}
	return $fScore;
}

//Follower : Following ratio
function calculateFfRatio($friends,$fol)
{
	if ($friends > $fol)
	{	$Ff=0;	}

	else
	{
		$Ffvar = $friends / ($fol * 0.25);

		if ($Ffvar >= 1)
			{	$Ff = 5;	}
		else
			{	$Ff = 5 * $Ffvar;	}
	}

	return $Ff;
}

//Count Retweets in user timeline and other user mentions
function calcRTsandOtherMention(&$settings,&$word,&$totalretweets,&$totaltweets,&$totalusermentions,&$RTScore,&$totalusermentionsSc,&$folcount,&$friendscount)
{	
	//echo "$word<br>";
	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	$getfield = "?screen_name={$word}&count=200";
	$requestMethod = 'GET';

	$twitter = new TwitterAPIExchange($settings);
	$response = $twitter->setGetfield($getfield)
	                    ->buildOauth($url, $requestMethod)
	                   ->performRequest();
	$json_data = $response;
	$json = json_decode($json_data);
	//$arraycount = count($json);
	//echo "Array COUNT: $arraycount";
	////// End user timeline details
	//$data = json_decode($json);

	$folcount = $json[0]->user->followers_count;

	$friendscount = $json[0]->user->friends_count;

	foreach ($json as $item) {
	//echo $item->retweet_count;
	//echo "<br/>\n";
	//echo $json[0]->retweet_count;
	$totaltweets++;
	$mystring = $item->text;
	$find = 'RT';
	$pos = strpos($mystring, $find);

	if ($pos !== false)
		{	//echo "Yes\n";
		}
	else
		{	//echo "No\n";
			$totalretweets = $totalretweets + $item->retweet_count;

			if ($item->in_reply_to_screen_name != null)	//if there's a user mentioned in a tweet
			{	$totalusermentions += 1;	}
		}

	//echo "<br/>\n";
	}

	//RETWEETS CALCULATION
	if($totalretweets > $totaltweets)
	{
		$RTScore = 30 - ($totaltweets/$totalretweets *30);
	}

	elseif ($totalretweets < $totaltweets) 
	{
		$RTScore = ($totalretweets / $totaltweets) * 10;	
	}

	else
	{
		$RTScore = 15;
	}

	if ($totalusermentions >= ($totaltweets * 0.5))
	{	$totalusermentionsSc = 10;	}

	else
	{
		$totalusermentionsSc = ($totalusermentions/($totaltweets * 0.5));
		$totalusermentionsSc *= 10;
	}

	$RTScore = round($RTScore,3);
}

//calculation of mention of other users
function calcOtherUserMentions(&$totalusermentions,&$totaltweets,&$totalusermentionsSc)
{
	
}

//Search for user mntions
function calcMentionsScore(&$settings,&$word,&$totalmentions,&$totalmentionsSc)
{

	$word = "@" . $word;
	//echo "Username is $word<br/>\n";
	for ($i=0; $i<10; $i++)
	{
		if($i==0)
		{
			$url2 = 'https://api.twitter.com/1.1/search/tweets.json';
			$getfield2 = "?q={$word}&count=100";
			$requestMethod2 = 'GET';

			$twitter = new TwitterAPIExchange($settings);
			$response2 = $twitter->setGetfield($getfield2)
			                    ->buildOauth($url2, $requestMethod2)
			                   ->performRequest();
			$json_data2 = $response2;
			$json2 = json_decode($json_data2);

			if (!empty($json2->statuses))
			{
				$arraycount2 = count($json2->statuses);
				//echo "$i:$arraycount2<br>";
				$totalmentions += $arraycount2;
			}
		}

		else
		{
			$url2 = 'https://api.twitter.com/1.1/search/tweets.json';
			$getfield2 = "?q={$word}&count=101&max_id=$maxID";
			$requestMethod2 = 'GET';

			$twitter = new TwitterAPIExchange($settings);
			$response2 = $twitter->setGetfield($getfield2)
			                    ->buildOauth($url2, $requestMethod2)
			                   ->performRequest();
			$json_data2 = $response2;
			$json2 = json_decode($json_data2);

			if (!empty($json2->statuses))
			{
				$arraycount2 = count($json2->statuses);
				//echo "$i:$arraycount2<br>";
				$totalmentions += ($arraycount2 - 1);
			}
		}

		//echo $json2->statuses[$arraycount2-1]->created_at;
		//echo "<br/>\n";
		if (!empty($json2->statuses))
		{
			$maxID = $json2->statuses[$arraycount2-1]->id_str;
		}

		else
			break;
	}


	$totalmentionsSc = $totalmentions/1000 * 30;
}

//echo "Total mentions of user on twitter: $totalmentions <br/>\n";


/***
$url2 = 'https://api.twitter.com/1.1/search/tweets.json';
$getfield2 = "?q={$word}&count=100";
$requestMethod2 = 'GET';


$twitter = new TwitterAPIExchange($settings);
$response2 = $twitter->setGetfield($getfield2)
                    ->buildOauth($url2, $requestMethod2)
                   ->performRequest();
$json_data2 = $response2;
$json2 = json_decode($json_data2);
$arraycount2 = count($json2->statuses);

echo "Total Mentions: $arraycount2";
echo "<br>\n";
echo $json2->statuses[$arraycount2-1]->text;
echo "<br>\n";
echo $json2->statuses[$arraycount2-1]->created_at;
echo "<br>\n";
***/

if ( isset( $_POST["Submitt"] ) ) 
{ 
	$totalinf =0;
	$folcount =0;
	$folScore =0;
	$friendscount =0;
	$Ffratio = 0;
	$totaltweets =0;
	$totalretweets = 0;
	$RTScore = 0;
	$totalmentions = 0;
	$totalusermentions = 0;
	$totalusermentionsSc = 0;

	ini_set('max_execution_time', 300);
	$word = $_POST["keyword"];
	calcRTsandOtherMention($settings,$word,$totalretweets,$totaltweets,$totalusermentions,$RTScore,$totalusermentionsSc,$folcount,$friendscount);
	?>

	<table border = "1">
		<tr>
	<?php
	echo "<td class = 'firstcol'>Username</td><td class = 'secondcol'>$word</td>\n";
	echo "</tr><tr>\n";
	echo "<td class = 'firstcol'>Number of Followers</td><td class = 'secondcol'>$folcount</td></tr><tr>";
	echo "<td class = 'firstcol'>Number of Users Following</td><td class = 'secondcol'>$friendscount</td></tr><tr>";
	//echo "<br/>\n";

	//echo "Number of Followers: $folcount <br>\n";
	$folScore = calculateFols($folcount);
	$folScore = round($folScore,3);
	echo "<td class = 'firstcol'>Followers score</td><td class = 'secondcol'>$folScore</td></tr>\n";

	
	echo "<tr><td class = 'firstcol'>Total retweets</td><td class = 'secondcol'>$totalretweets</td></tr>";
	echo "<tr><td class = 'firstcol'>Total retweets Score</td><td class = 'secondcol'>$RTScore</td></tr>";

	//CHANGE DENOMINATOR IF TWEET SEARCH COUNT IS ALTERED.
	calcMentionsScore($settings,$word,$totalmentions,$totalmentionsSc);
	echo "<tr><td class = 'firstcol'>User mentioned on Twitter</td><td class = 'secondcol'>$totalmentions</td></tr>";
	echo "<tr><td class = 'firstcol'>User mentioned on Twitter Score</td><td class = 'secondcol'>$totalmentionsSc</td></tr>";


	echo "<tr><td class = 'firstcol'>Timeline user mentions</td><td class = 'secondcol'>$totalusermentions</td></tr>\n";
	$totalusermentionsSc = round($totalusermentionsSc,3);
	echo "<tr><td class = 'firstcol'>Timeline user mentions Score</td><td class = 'secondcol'>$totalusermentionsSc</td></tr>\n";

	$Ffratio = calculateFfRatio($friendscount,$folcount);
	$Ffratio = round($Ffratio,3);
	echo "<tr><td class = 'firstcol'>Followers:Following ratio Score</td><td class = 'secondcol'>$Ffratio</td></tr>";
	echo "</table>";
	//$totalretweets = ($totalretweets / 100) * 30;
	//$totalmentions = $totalmentions / 100 * 30;
	$totalinf = $RTScore + $totalmentionsSc + $folScore + $Ffratio + $totalusermentionsSc;
	$finalscore = round($totalinf,3);
	echo "<br>\n<div class = 'score'><font size=12>User Influence = $finalscore </font></div>";

	//var_dump(json_decode($response2));
	//var_dump(json_decode($response));

	//$arraycount = count($json->statuses);
	//echo $arraycount;
	//var_dump($json);
	//echo $json->statuses[3]->metadata->text;
	//echo $json->statuses[0];

	//echo $response;

	/*REFERENCES
	echo $json[0]->id_str; | To select first element in json array
	$getfield = "?screen_name={$word}&count=32"; |  more than one query
	*/

}
?>


</body>
</html>
