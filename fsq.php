<?php
//session_start();
//new fsq();

class fsq{
	private $radius = 500;
	private $limit = 100;
	private $oauth_token = 'ZT22HEEOMTDHQ4CCSXNBH23ZN3DMH3Z0HRY41NJUA3W0DO0O&v=20151007';

	private $data;
	private $ll;
	private $areaid;
	private $userid;

	private $foursquare_contents;

	private $connection;
	private $query1 = "";
	private $query2 = "";

	function fsq(){
		//print_r($this->data);
		$this->dbConnect();
		$this->setData();
		$this->getFsqContents();

	}

	function setData(){
		$this->data = json_decode($_SESSION["data"],true);

		//return $data;
	}

	function dbConnect(){

		//Connection to database
		$dbHost = 'localhost';
		$dbUser = 'hotcity';
		$dbPass = 'hotcity@1234';
		$dbName = 'hotcityAR';
		$this->connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName)
				or die('Error Connecting to MySQL DataBase');
		mysqli_set_charset($this->connection, "utf8");
		
		/*$db_server["host"] = "localhost"; //database server
		$db_server["username"] = "hotcity"; // DB username
		$db_server["password"] = "hotcity@1234"; // DB password
		$db_server["database"] = "hotcityAR";// database name

		//sundesh sthn vash dedomenwn.
		$connection = mysql_connect($db_server["host"], $db_server["username"], $db_server["password"]);
					  mysql_query ('SET CHARACTER SET utf8');
		 			  mysql_query ('SET COLLATION_CONNECTION=utf8_general_ci');
		 			  mysql_select_db($db_server["database"], $connection);

		if(!$connection) die('Could not connect to MySQL:' . mysql_error() );
		*/
		//return $connection;

	}

	//---Returns Fsq's mastercategory-id given as input a subcategory-id
	function getFsqMasterCategory($json_output, $subcategory){

		foreach ($json_output->response->categories as $i)
		{
			if($subcategory == $i->id){
				return $i->id;
			}

			foreach($i->categories as $j)
			{
				if($subcategory == $j->id){
					return $i->id;
				}

				foreach($j->categories as $k)
				{
					if($subcategory == $k->id){
						return $i->id;
					}
				}
			}
		}
		return null;
	}

	//---Prints Fsq's master categories in tree view
	function printFsqMasterCategories($json_output){
		
		//print_r($json_output);
		foreach ($json_output->response->categories as $i)
		{
			echo $i->name."</br>";
			$mastercatsql.="(\"$i->id\", \"$i->name\"),";
			foreach($i->categories as $j)
			{
				echo ".....".$j->name."</br>";
				$subcatsql.="(\"$j->id\", \"$j->name\", \"$i->id\"),";
				foreach($j->categories as $k)
				{
					echo ".....-------".$k->name."</br>";
					$subcatsql.="(\"$k->id\", \"$k->name\", \"$i->id\"),";
				}
			}
		}
	}


	function getFsqContents(){
		
		//Set 4sq's parameters to fetch POIs for a specific area
		//$ll = '38.246284,21.734941';
		$this->ll = $this->data['centerlat'].",".$this->data['centerlng'];
		$this->areaid = $this->data['areaid'];
		$this->userid = $this->data['userid'];
		
		$json_cat = file_get_contents("https://api.foursquare.com/v2/venues/categories?oauth_token=ZT22HEEOMTDHQ4CCSXNBH23ZN3DMH3Z0HRY41NJUA3W0DO0O&v=20151014");
		$json_output = json_decode($json_cat);

		//$this->printFsqMasterCategories($json_output);

		$foursquare_contents = file_get_contents('https://api.foursquare.com/v2/venues/explore?ll='.$this->ll.'&radius='.$this->radius.'&limit='.$this->limit.'&oauth_token='.$this->oauth_token);
		$json = json_decode($foursquare_contents);

		//print_r($json);

		$response = $json->response;

		
		//print_r($response);
		echo "<script type='text/javascript'>
			var markers = [];
			var info = [];
			";
		

		$totalResults = $response->totalResults;

		if($totalResults == 0) return;

		$items = $response->groups[0]->items;
		
		$this->query1 .= "INSERT INTO pois(name, poi_id, latitude, longitude, category, rating, checkins) VALUES ";
		$this->query2 .= "INSERT INTO area_pois(area_id, poi_id) VALUES";		

		foreach($items as $item){
	
			//$id = isset($item->venue->id) ? $item->venue->id : 'no id';
			//$name = isset($item->venue->name) ? addslashes($item->venue->name) : 'no name';
			//$distance = isset($item->venue->location->distance;) ? $item->venue->location->distance; : 'no distance';
			//$category = isset($item->venue->categories[0]->name) ? $item->venue->categories[0]->name : 'no category';
			//$lat = isset($item->venue->location->lat) ? $item->venue->location->lat : 0.0;
			//$lng = isset($item->venue->location->lng) ? $item->venue->location->lng : 0.0;
			//print_r($item);
			$poi_id = $item->venue->id;
			$name = addslashes($item->venue->name);
			$distance = $item->venue->location->distance;
			$category = addslashes($item->venue->categories[0]->id);
			$categoryName = addslashes($item->venue->categories[0]->name);
			$lat = $item->venue->location->lat;
			$lng = $item->venue->location->lng;
			$rating = isset($item->venue->rating) ? $item->venue->rating : 0 ;
			$checkins = $item->venue->stats->checkinsCount;
			//$ratingColor = $item->venue->ratingColor;

			$mastercat = $this->getFsqMasterCategory($json_output, $category);
			//('4d4b7104d754a06370d81259', 'Τέχνες & Διασκέδαση', 'Arts & Entertainment'),
			//('4d4b7105d754a06372d81259', 'Πανεπιστήμιο & Κολλέγιο', 'College & University'),
			//('4d4b7105d754a06374d81259', 'Φαγητό', 'Food'),
			//('4d4b7105d754a06377d81259', 'Εξωτερικοί χώροι', 'Outdoors & Recreation'),
			//('4d4b7105d754a06376d81259', 'Νυχτερινή ζωή', 'Nightlife Spot'),
			//('4d4b7105d754a06375d81259', 'Γραφεία & Επιχειρήσεις', 'Professional & Other Places'),
			//('4e67e38e036454776db1fb3a', 'Κατοικίες', 'Residence'),
			//('4d4b7105d754a06378d81259', 'Καταστήματα & Υπηρεσίες', 'Shop & Service'),
			//('4d4b7105d754a06379d81259', 'Μεταφορές & Μετακίνηση', 'Travel & Transport'),
			//('4d4b7105d754a06373d81259', 'Δρώμενο', 'Event');

			
			echo "markers.push( new google.maps.LatLng('".$lat."','".$lng."') );";
			echo "info.push({id:'".$poi_id."', 
							 name: '".$name."', 
							 checkins: '".$checkins."',
							 category: '".$categoryName."',
							 rating: '".$rating."'
							});
				 ";
			

			
       		$this->query1 .= "('".$name."','".$poi_id."','".$lat."','".$lng."','".$mastercat."','".$rating."','".$checkins."'),";
       		$this->query2 .= "('".$this->areaid."','".$poi_id."'),";
		}

		
		//$ne_lat = $data["boundsnelat"];
		//$ne_lng = $data["boundsnelng"];

		//$sw_lat = $data["boundsswlat"];
		//$sw_lng = $data["boundsswlng"];

		//echo "var ne = new google.maps.LatLng('".$ne_lat."','".$ne_lng."'); 
		//var sw = new google.maps.LatLng('".$sw_lat."','".$sw_lng."'); ";

		//echo "var ne = new google.maps.LatLng(38.250073, 21.737679); ";
		//echo "var sw = new google.maps.LatLng(38.245081, 21.735177); ";


		echo "</script>";
		

		$this->query1 = substr($this->query1, 0, -1).";";
		$this->query2 = substr($this->query2, 0, -1).";";
		$this->dbInsert();
	}

	function dbInsert(){
		
		$query3 = "INSERT INTO area_user(area_id, user_id) 
				VALUES ('".$this->areaid."','".$this->userid."');";
		$bound1 = $this->data["nelat"].','.$this->data["nelng"];
		$bound2 = $this->data["swlat"].','.$this->data["swlng"];
		$query4 = "INSERT INTO area(area_id, up_left, down_right, created_by) 
				VALUES ('".$this->areaid."','".$bound1."','".$bound2."','".$_SESSION["username"]."');";
		
		$result4 = mysqli_query($this->connection,$query4);//1st: Fill 'area' table 
		$result1 = mysqli_query($this->connection,$this->query1);//2nd fill 'pois' table
		$result2 = mysqli_query($this->connection,$this->query2);// 3rd fill 'area_pois' table
		$result3 = mysqli_query($this->connection,$query3);//4th fill 'area_user' table

		if(!$result1) mysqli_error($this->connection);	
		if(!$result2) mysqli_error($this->connection);	
		if(!$result4) mysqli_error($this->connection);	
		//if(!$result1 && !$result2 && !$result3 && !$result4) mysql_error();	

		$this->dbClose();

	}

	function dbClose(){
		mysqli_close($this->connection);
	}


}




?>