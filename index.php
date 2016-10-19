<?php
	require_once "dbconnect.php";
	
	$connect = @new mysqli($db_host, $db_user, $db_password, $db_name);

	if ($connect->connect_errno != 0)
	{
		echo "Connection with database unavailable. Please try again later...";
		exit();
	} 


	if (isset($_POST['givenQuery']))
	{
		$placeholder = $_POST['givenQuery'];
		$value = $placeholder;
	}
	else
	{
		$placeholder = "Search all the GIFs...";
		$value = "";
	}

	if (isset($_POST['like']))
	{	
		$postID = $_POST['like'];

		$postID = htmlentities($postID, ENT_QUOTES, "utf-8");
		$postID - mysqli_real_escape_string($connect, $postID);

		$sql_like = "UPDATE gif SET likes=(likes+1) WHERE id='$postID'";
		$sql_liked = @$connect->query($sql_like);
		unset($_POST['like']);
	}

	if (isset($_POST['dislike']))
	{	
		$postID = $_POST['dislike'];
		
		$postID = htmlentities($postID, ENT_QUOTES, "utf-8");
		$postID - mysqli_real_escape_string($connect, $postID);

		$sql_dislike = "UPDATE gif SET dislikes=(dislikes+1) WHERE id='$postID'";
		$sql_disliked = @$connect->query($sql_dislike);
		unset($_POST['dislike']);
	}

	if (isset($_POST['likeNEW']))
	{	
		$postID = $_POST['likeNEW'];
		
		$postID = htmlentities($postID, ENT_QUOTES, "utf-8");
		$postID - mysqli_real_escape_string($connect, $postID);

		$sql_likeNEW = "INSERT INTO gif (id, likes, dislikes) VALUES ('$postID', '1', '0')";
		$sql_likedNEW = @$connect->query($sql_likeNEW);
		unset($_POST['likeNEW']);
	}

	if (isset($_POST['dislikeNEW']))
	{	
		$postID = $_POST['dislikeNEW'];

		$postID = htmlentities($postID, ENT_QUOTES, "utf-8");
		$postID - mysqli_real_escape_string($connect, $postID);

		$sql_dislikeNEW = "INSERT INTO gif (id, likes, dislikes) VALUES ('$postID', '0', '1')";
		$sql_dislikedNEW = @$connect->query($sql_dislikeNEW);
		unset($_POST['dislikeNEW']);
	}

?>
<!DOCTYPE HTML>
<html lang="PL">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>Wyszukiwarka GIFów - Patryk Żurawik</title>
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/fontello.css">
		<link href="https://fonts.googleapis.com/css?family=Exo:400,700|Inconsolata:400,700|Lobster|Shadows+Into+Light" rel="stylesheet">
		<script type="text/javascript" src="main.js"></script>
	</head>
	<body>
		<div id="container">
			<div id="gifImage">
				<img src="gif.gif" width="300px">
			</div>
			
			<div id="logo">
				<div id="logoTop">
					<a href="index.php">GIFs search</a> 
				</div>
				<div id="logoBottom">
					driven by: <a href="http://giphy.com" target="_blank">GIPHY.COM</a>
				</div>
			</div>

			<div id="searchBar">
				<form id="searchForm" method="POST">
					<input type="text" id="searchBarInput" name="givenQuery" placeholder="<?php echo $placeholder;?>" value="<?php echo $value;?>" autofocus>
					<input type="number" id="searchBarNumber" name="givenNumber" min="1" max="100" value="10">
					<button type="submit" id="searchBarSubmit"><i class="icon-search"></i></button>
				</form>
			</div>

			<div id="searchResults">
				<?php
					
					if(isset($_POST['givenQuery']))
					{
						$givenQuery = $_POST['givenQuery'];
						$givenQuery = str_replace(" ", "+", $givenQuery);
						$givenQuery = str_replace(",", "+", $givenQuery);
						
						$givenNumber = $_POST['givenNumber'];
						$queryToSend = 'http://api.giphy.com/v1/gifs/search?q=' . $givenQuery . '&api_key=dc6zaTOxFJmzC&limit=' . $givenNumber . '&fmt=json';

						$ch = curl_init();

						curl_setopt($ch, CURLOPT_URL, $queryToSend);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_Setopt($ch, CURLOPT_RETURNTRANSFER, true);

						$results = curl_exec($ch);

						curl_close($ch);

						$results = json_decode($results, true);

						$responseCode = $results['meta']['status'];
						if ( ($responseCode>=400 && $responseCode<=418) || ($responseCode==451) || ($responseCode>=500 && $responseCode<=511) ) 
						{
							echo "Connection with Giphy.com API unavailable. Please try again later...";
							exit();
						}

						$found = 0;

						for ($i = 0; $i < $givenNumber; $i++)
						{
							if(isset($results['data'][$i]['images']['fixed_height']['mp4']))
							{
								$found = 1;
								$id = $results['data'][$i]['id'];

								$id = htmlentities($id, ENT_QUOTES, "utf-8");
								$id - mysqli_real_escape_string($connect, $id);
								
								$sql = "SELECT * FROM gif WHERE id='$id'";
								$sql_result = @$connect->query($sql);


								if ($sql_result->num_rows > 0)
								{
									$row = $sql_result->fetch_assoc();
									$likes = $row['likes'];
									$dislikes = $row['dislikes'];
									echo '<div class="gif" id="' . $id . '"><video autoplay loop><source src="' . $results['data'][$i]['images']['fixed_height']['mp4'] . '"> type="video/mp4"</video><br>
											<form name="rate" method="POST">
												<button type="submit" class="like" name="like" value="' . $id . '">
													<i class="icon-thumbs-up-alt"></i>
												</button>' . $likes .  '
												<button type="submit" class="dislike" name="dislike" value="' . $id . '">
													<i class="icon-thumbs-down-alt"></i>
												</button>' . $dislikes . '
											</form>
										  </div>';
								}
								else
								{
									echo '<div class="gif" id="' . $id . '"><video autoplay loop><source src="' . $results['data'][$i]['images']['fixed_height']['mp4'] . '"> type="video/mp4"</video><br>
											<form name="rateNEW" method="POST">
												<button type="submit" class="like" name="likeNEW" value="' . $id . '">
													<i class="icon-thumbs-up-alt"></i>
												</button>' . '0' . '
												<button type="submit" class="dislike" name="dislikeNEW" value="' . $id . '">
													<i class="icon-thumbs-down-alt"></i>
												</button>' . '0' . '
											</form>
										  </div>';
																		
								}
							}
							else
							{
								if ($found==0) echo "Nothing found... :( Try another query!";
								break;		
							}
						}
					}
				?>
			</div>

			<div id="footer">
				autor: Patryk Żurawik
			</div>

		</div>

	</body>
</html>