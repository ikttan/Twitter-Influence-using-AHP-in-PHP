# influence-ahp
<h1>Twitter Infuence Scoring using AHP</h1>

PHP souce code for computing the influence of a twitter user on specific keywords using the Analytical Hierarchy Process (AHP).  The code relies on <a href="https://github.com/J7mbo/twitter-api-php">TwitterAPIExchange</a>.  Keywords can have synonyms (current version is broken).  This also includes a simplified Edelmann implementation for comparison (normalised data).

Provide the values for before using the code:-

oauth_access_token

oauth_access_token_secret

consumer_key

consumer_secret

<p>To do:</p>

<ol>
<li>Catch the division by zero in ahp.php (circa line 174 & 336)</li>
<li>Null exception in about line 1055 in ahp.php</li>
</ol>

