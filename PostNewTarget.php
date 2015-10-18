<?php
//session_start();
require_once 'HTTP/Request2.php';
require_once 'SignatureBuilder.php';

class PostNewTarget{

	//Server Keys
	private $access_key 	= "d7c2d2e2dd91715402ffe878a6691d154d5b6f7d";//"29faf51d6cb7374ec3c28ab1b61ae4bdcc38754e";
	private $secret_key 	= "a16ef432047c3d57c7e88a7e67a27c34b51000b6";//"0fa0946edf3d2e766ed8c46524f481f4cfb4229c";
	
	//private $targetId 		= "eda03583982f41cdbe9ca7f50734b9a1";
	private $url 			= "https://vws.vuforia.com";
	private $requestPath 	= "/targets";
	private $request;       // the HTTP_Request2 object
	private $jsonRequestObject;
	
	private $targetName 	= " ";
	private $imageLocation = "https://maps.googleapis.com/maps/api/staticmap?center=";
	private $basedir;
	private $data;
	
	function PostNewTarget(){
		$this->data = json_decode($_SESSION['data'],true);
		$this->basedir = "Targets/".$this->data["userid"]."/";
		//print_r($this->data);
		$date = date('mdY_h:i:s', time());
		$this->targetName = $this->data["userid"]."_".$date;
		$this->imageLocation .= $this->data["centerlat"].",".$this->data["centerlng"]."&zoom=16&size=640x640&format=jpg&style=gamma:0.5 ";
		/*$points = array("areaid"=>$data["areaid"],"point1"=>array("p1lat"=>$data['nelat'],'p1lng'=>$data['nelng']),
		"point2"=>array("p2lat"=>$data['nelat'],'p2lng'=>$data['swlng']),
		"point3"=>array("p3lat"=>$data['swlat'],'p3lng'=>$data['nelng']),
		"point4"=>array("p4lat"=>$data['swlat'],'p4lng'=>$data['swlng']));
		
		print_r($points);
		*/
		$metadata = array("userid"=>$this->data['userid'],"targetname"=>$this->targetName,"areaid"=>$this->data['areaid'],"mlat"=>$this->data["mlat"],"mlng"=>$this->data['mlng'],"zeropointlat"=>$this->data['zero_point_lat'],"zeropointlng"=>$this->data['zero_point_lng']);
		$this->jsonRequestObject = json_encode( array( 'width'=>640.0 , 'name'=>$this->targetName , 'image'=>$this->getImageAsBase64() , 'application_metadata'=>base64_encode(json_encode($metadata)) , 'active_flag'=>1 ) );

		$this->execPostNewTarget();
		
	}
	
	function getImageAsBase64(){

		$file = file_get_contents( $this->imageLocation );
		$this->storeToServer($file);
		if( $file ){
			$file = base64_encode( $file );
		}
		return $file;
	}

	public function execPostNewTarget(){

		$this->request = new HTTP_Request2();
		$this->request->setMethod( HTTP_Request2::METHOD_POST );
		$this->request->setBody( $this->jsonRequestObject );

		$this->request->setConfig(array(
				'ssl_verify_peer' => false
		));
		
		$this->request->setURL( $this->url . $this->requestPath );
		// Define the Date and Authentication headers
		$this->setHeaders();
	
		try {
			$response = $this->request->send();

			if (200 == $response->getStatus() || 201 == $response->getStatus() ) {
				$targetid = json_decode($response->getBody(),true);
				$target_id = $targetid["target_id"];
				$this->dbInsert($target_id);
				//echo $response->getBody();
			} else {
				echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
						$response->getReasonPhrase(). ' ' . $response->getBody();
			}
		} catch (HTTP_Request2_Exception $e) {
			echo 'Error: ' . $e->getMessage();
		}
	}

	private function setHeaders(){
		$sb = 	new SignatureBuilder();
		$date = new DateTime("now", new DateTimeZone("GMT"));

		// Define the Date field using the proper GMT format
		$this->request->setHeader('Date', $date->format("D, d M Y H:i:s") . " GMT" );
		
		$this->request->setHeader("Content-Type", "application/json" );
		// Generate the Auth field value by concatenating the public server access key w/ the private query signature for this request
		$this->request->setHeader("Authorization" , "VWS " . $this->access_key . ":" . $sb->tmsSignature( $this->request , $this->secret_key ));

	}
	
	private function storeToServer($file){
		$ext = ".jpg";
		$img = $this->data['areaid'].$ext;
		if (!is_dir($this->basedir)) {
		  // dir doesn't exist, make it
		  mkdir($this->basedir,0777);
		}
		//echo $this->basedir.$img;
		file_put_contents($this->basedir.$img,$file);
	}
	
	private function dbInsert($targetid){
		$dbHost = 'localhost';
		$dbUser = 'hotcity';
		$dbPass = 'hotcity@1234';
		$dbName = 'hotcityAR';
		$dbC = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName)
				or die('Error Connecting to MySQL DataBase');
		mysqli_set_charset($dbC, "utf8");
		$query = "INSERT INTO user_target (user_id,target_id,area_id) VALUES ('".$this->data['userid']."','".$targetid."','".$this->data['areaid']."');";
		mysqli_query($dbC,$query);
		
		mysqli_close($dbC);
	}
}
?>
