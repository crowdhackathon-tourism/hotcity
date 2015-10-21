<?php

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
		$dbUser = 'username';
		$dbPass = 'pass';
		$dbName = 'dbARname';
		$this->connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName)
				or die('Error Connecting to MySQL DataBase');
		mysqli_set_charset($this->connection, "utf8");
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

		$totalResults = $response->totalResults;

		if($totalResults == 0) return;

		$items = $response->groups[0]->items;
		
		$this->query1 .= "INSERT INTO pois(name, poi_id, latitude, longitude, category, rating, checkins) VALUES ";
		$this->query2 .= "INSERT INTO area_pois(area_id, poi_id) VALUES";		

		foreach($items as $item){
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

			
			
       		$this->query1 .= "('".$name."','".$poi_id."','".$lat."','".$lng."','".$mastercat."','".$rating."','".$checkins."'),";
       		$this->query2 .= "('".$this->areaid."','".$poi_id."'),";
		}


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