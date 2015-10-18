<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();

new POI();
class POI{

	private $radius = 500;
	private $limit = 100;
	private $oauth_token = 'ZT22HEEOMTDHQ4CCSXNBH23ZN3DMH3Z0HRY41NJUA3W0DO0O&v=20151007';

	private $data;
	private $ll;
	private $areaid;
	private $userid;
	private $poiArray = array();

	private $foursquare_contents;

	function POI(){
		$this->data = json_decode($_SESSION["data"],true);
		//print_r($this->data);
		$this->getFsqContents();
	}
	
	function getFsqContents(){
		$this->ll = $this->data['centerlat'].",".$this->data['centerlng'];
		$this->areaid = $this->data['areaid'];
		$this->userid = $this->data['userid'];
		
		$json_cat = file_get_contents("https://api.foursquare.com/v2/venues/categories?oauth_token=ZT22HEEOMTDHQ4CCSXNBH23ZN3DMH3Z0HRY41NJUA3W0DO0O&v=20151014");
		$json_output = json_decode($json_cat);

		$foursquare_contents = file_get_contents('https://api.foursquare.com/v2/venues/explore?ll='.$this->ll.'&radius='.$this->radius.'&limit='.$this->limit.'&oauth_token='.$this->oauth_token);
		$json = json_decode($foursquare_contents);

		$response = $json->response;

		$totalResults = $response->totalResults;

		if($totalResults == 0) return;

		$items = $response->groups[0]->items;

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

			$this->poiArray[] = array("id"=>$poi_id,"name"=>$name,"distance"=>$distance,"category"=>$category,"mastercat"=>$mastercat,
			"categoryName"=>$categoryName,"lat"=>$lat,"lng"=>$lng,"rating"=>$rating,"chekcins"=>$checkins);
		}
		
		echo json_encode($this->poiArray);
	}
	
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

}

/*$object = new stdClass();
			$object->poi_id = $poi_id;
			$object->name = $name;
			$object->distance = $distance;
			$object->category = $category;
			$object->categoryName = $categoryName;
			$object->lat = $lat;
			$object->lng = $lng;
			$object->rating = $rating;
			$poiArray[] = $object; */

?>