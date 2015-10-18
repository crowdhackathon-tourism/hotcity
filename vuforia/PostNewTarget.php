<?php
session_start();
require_once 'HTTP/Request2.php';
require_once 'SignatureBuilder.php';

//new PostNewTarget();
class PostNewTarget{

	//Server Keys
	private $access_key 	= "46aece67c725ef7f281c96b6f8d9c5e7fd3ea744";
	private $secret_key 	= "ec36604dc8e16e2c4cbbeccd3850c114031c42de";
	
	//private $targetId 		= "eda03583982f41cdbe9ca7f50734b9a1";
	private $url 			= "https://vws.vuforia.com";
	private $requestPath 	= "/targets";
	private $request;       // the HTTP_Request2 object
	private $jsonRequestObject;
	
	private $targetName 	= " ";
	private $imageLocation = "https://maps.googleapis.com/maps/api/staticmap?center=";
	
	//private $data = json_decode($_SESSION['data'],true);
	
	function PostNewTarget(){
		/*$data = file_get_contents('php://input');
		$data = json_decode($data,true);
		$date = date('mdY_h:i:s', time());
		$areaid = md5($data['userid'].$date);
		$_SESSION["areaid"] = $areaid;
		*/
		print_r($data);
		$date = date('mdY_h:i:s', time());
		$this->targetName = $data["userid"]."_".$date;
		$this->imageLocation .= $data["centerlat"].",".$data["centerlng"]."&zoom=16&size=640x640&format=jpg ";
		
		/*$points = array("areaid"=>$data["areaid"],"point1"=>array("p1lat"=>$data['nelat'],'p1lng'=>$data['nelng']),
		"point2"=>array("p2lat"=>$data['nelat'],'p2lng'=>$data['swlng']),
		"point3"=>array("p3lat"=>$data['swlat'],'p3lng'=>$data['nelng']),
		"point4"=>array("p4lat"=>$data['swlat'],'p4lng'=>$data['swlng']));
		
		print_r($points);
		*/
		$metadata = array("userid"=>$data['userid'],"areaid"=>$data['areaid']);
		//print_r($metadata);
		//echo "<br><br>".base64_encode(json_encode($metadata))."<br><br>";
		$this->jsonRequestObject = json_encode( array( 'width'=>640.0 , 'name'=>$this->targetName , 'image'=>$this->getImageAsBase64() , 'application_metadata'=>base64_encode(json_encode($metadata)) , 'active_flag'=>1 ) );

		$this->execPostNewTarget();
		
	}
	
	function getImageAsBase64(){

		$file = file_get_contents( $this->imageLocation );
		
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
		//print_r( $this->request->getBody());
	
		try {
			$response = $this->request->send();

			if (200 == $response->getStatus() || 201 == $response->getStatus() ) {
				echo "PostTarget: ".$_SESSION['areaid'];//echo $response->getBody();
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
}
?>
