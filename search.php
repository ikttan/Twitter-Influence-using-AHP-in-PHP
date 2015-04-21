<html>
#THIS WORKS, ENTER HASHTAG AND FIND ON TWITTER
<body>

<form action = "search.php" method = "post">

Enter keyword: <input type = "text" name = "keyword"/><br>
<input type="submit" name="Submit"/><br><br>
<?php

$word = $_POST["keyword"];
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
$url = 'https://api.twitter.com/1.1/search/tweets.json';
$getfield = "?q={$word}&count=100&geocode=3.120742,101.656950,100km";
$requestMethod = 'GET';
//echo $word;
echo "<br>\n";

$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)
                    ->buildOauth($url, $requestMethod)
                   ->performRequest();
$json_data = $response;
$json = json_decode($json_data);
$arraycount = count($json->statuses);

echo "<b>Total tweets retrieved: </b>" . $arraycount;
echo "<br>";
//echo $arraycount;

//$data = json_decode($json);

//THIS WORKS, OUTPUTS USERNAMESSS
foreach ($json->statuses as $item) {
	//echo "Data here!<br>\n";
	echo $item->user->screen_name ."<br /> \n";
    echo $item->user->location. "<br>";
	echo $item->text ."<br /> \n";
	echo "<br>\n";
}
/**
foreach ($data as $item) {     // ---- start foreach ---- 


echo "<div class=\"userdata\" style=\"clear:both; padding:10px; background:#FFFFFF; text-align:left;\"> \n"; 

echo "<img src=\"". $item->user->profile_image_url ."\" style=\"border:none; width:40px; float:left; padding:0px 8px 4px 0px;\" /> \n"; 

echo "User: ". $item->user->screen_name ."<br /> \n"; 

echo "Name: ". $item->user->name ."<br /> \n"; 

echo "Friends: ". $item->user->friends_count ."<br /> \n"; 

echo "Follower: ". $item->user->followers_count ."<br /> \n"; 

echo "<br /> &nbsp; \n"; 

echo "<span class=\"thetweet\" style=\"padding:8px; background:#DEDEDE;\"> \n"; 

echo $item->text ."\n";  // --- the Status Text = the tweet --- 

echo "</span> \n"; 

echo "<br /> &nbsp; \n"; 

echo "<br /> Date of Tweet: ". $item->created_at ." \n"; 

echo "<br /> Souce of Tweet: ". $item->source ." \n"; 

echo "<br /> &nbsp; \n"; 

echo "</div> \n"; 
}
**/

//$arraycount = count($json->statuses);
//echo $arraycount;
//var_dump($json);

//echo $json->statuses[3]->metadata->text;
//echo $json->statuses[0];
//var_dump(json_decode($response));
//echo $response;

?>
