<html>

<style type = "text/css">
	
	h1{
		text-align: center;
		color: #000066;
	}

	div{
		width:350px;
		height:350px;
		line-height: 4em;
		text-align: center;
		background-color: #ccffff;
		color: black;
		right:200px;
	}
	
	.first
	{	
		font-size: 150%;
		border : 1px solid grey;
		position: absolute;
		right:270px;
		background-image: url(lba.jpg);
	}

	.second
	{
		font-size: 150%;
		border : 1px solid grey;
		position: absolute;
		right:650px;
		background-image: url(lbh.jpg);
	}

	.first:hover {
		background-image: url(dba.jpg);
		color: white;
	}

	.second:hover {
		background-image: url(dbh.jpg);
		color: white;
	}

	html{
		height:100%;
		background: rgb(237,237,237); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(237,237,237,1) 1%, rgba(41,137,216,1) 42%, rgba(32,124,202,1) 57%, rgba(125,185,232,1) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,rgba(237,237,237,1)), color-stop(42%,rgba(41,137,216,1)), color-stop(57%,rgba(32,124,202,1)), color-stop(100%,rgba(125,185,232,1))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  rgba(237,237,237,1) 1%,rgba(41,137,216,1) 42%,rgba(32,124,202,1) 57%,rgba(125,185,232,1) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  rgba(237,237,237,1) 1%,rgba(41,137,216,1) 42%,rgba(32,124,202,1) 57%,rgba(125,185,232,1) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  rgba(237,237,237,1) 1%,rgba(41,137,216,1) 42%,rgba(32,124,202,1) 57%,rgba(125,185,232,1) 100%); /* IE10+ */
	background: linear-gradient(to bottom,  rgba(237,237,237,1) 1%,rgba(41,137,216,1) 42%,rgba(32,124,202,1) 57%,rgba(125,185,232,1) 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ededed', endColorstr='#7db9e8',GradientType=0 ); /* IE6-9 */

	}
</style>

<body>

<h1>Malaysian Tweet Influence Ranking</h1>

<h3 style = "text-align:center;"><strong>Select and option below</strong></h3>

<div onclick="location.href='edelman.php';" class ="first">User Influence via Edelmann
	</div>

<div onclick="location.href='ahp.php';" class ="second">Twitter Influence via AHP
	</div>

<form method="POST">
    <!--<button name="command" value="show_file_1"  >Twitter Influence via AHP</button>
    <button name="command" value="show_file_2" href="userinfluence.php">User Influence via Edelmann</button>
-->
</form>

</body>
</html>