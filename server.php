<?php
session_start();
$host = "dbslusarek.mysql.database.azure.com";
$db_user = "slusarek@dbslusarek";
$db_password = "zaq1@WSX";
$db_name = "crud";

$conn = mysqli_init();
mysqli_real_connect($conn, $host, $db_user, $db_password, $db_name, 3306);
if (mysqli_connect_errno($conn)) {
die('Failed to connect to MySQL: '.mysqli_connect_error());
}
function checkURL($input) {
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $input);
}
	$id = 0;
	$link = "";

	if (isset($_POST['send'])){
		
		$result = mysqli_query($conn,"SELECT * FROM info");

		$all_title = [];
		$all_pubDate = [];
		$all_description = [];
		$all_link = [];
		$all_site = [];

		while ($row = mysqli_fetch_array($result)) {
			$feeds = simplexml_load_file($row["link"]);
			if(!empty($feeds)){

				$site = $feeds->channel->title;
				$sitelink = $feeds->channel->link;

					foreach ($feeds->channel->item as $item) {
	
					$title = $item->title;
					$link = $item->link;
					$description = $item->description;
					$postDate = $item->pubDate;
					$pubDate = date('D, d M Y',strtotime($postDate));
				}
			}
			array_push($all_site, $site);
			array_push($all_title, $title);
			array_push($all_pubDate, $pubDate);
			array_push($all_description, $description);
			array_push($all_link, $link);
		}
		
		$all_data = '';

		for($i = 0; $i < count($all_site); $i++){
			$all_data .= '<h1>'.$all_site[$i].'</h1><br>'.$all_title[$i].' '.$all_pubDate[$i].'<br>'.implode(' ', array_slice(explode(' ', $all_description[$i]), 0, 20)) .'...<a href="'.$all_link[$i].'">Czytaj więcej...</a><br><br>';
		}

		$email = $_POST['email'];
		$url = 'https://api.sendgrid.com/';
 		$user = getenv("SENDGRID_LOGIN");
 		$pass = getenv("SENDGRID_PASSWORD");

		$params = array(
			'api_user' => $user,
			'api_key' => $pass,
			'to' => $email,
			'subject' => 'RSS links',
			'html' => $all_data, 
			'text' => 'test',
			'from' => 'slusarekdavid@gmail.com',
		);
	

		$request = $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);

		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);

		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);

		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

		// obtain response
		$response = curl_exec($session);
		curl_close($session);

		// print everything out
		print_r($response);
				
		header('location: index.php');
	}

	
	if (isset($_POST['save'])) {
		$link = $_POST['link'];
		if (@simplexml_load_file($link)!=true){
			$_SESSION['message'] = "This is not a website with RSS";
			header('location: index.php');
		}
		else {
			mysqli_query($conn, "INSERT INTO info (link) VALUES ('$link')"); 
			$_SESSION['message'] = "Saved";
			header('location: index.php');
		}
	}

	if (isset($_GET['del'])) {
        $id = $_GET['del'];
        
		mysqli_query($conn, "DELETE FROM info WHERE id=$id");
		$_SESSION['message'] = "Deleted!"; 
		header('location: index.php');
    }
    
?>