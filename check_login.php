<?php
session_start(); 
$dbHost = 'localhost';
$dbUser = 'username';
$dbPass = 'pass';
$dbName = 'dbARname';
$dbC = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName)
        or die('Error Connecting to MySQL DataBase');
		
mysqli_set_charset($dbC, "utf8");
$username = $_POST['username']; //Set UserName
$password = $_POST['password']; //Set Password
$msg ='';

session_start(); 
	
if(isset($username, $password)) {
    
   	$sql="SELECT * FROM user WHERE username='$username' and password='$password'"; 
	$result=mysqli_query($dbC,$sql);
	$count=mysqli_num_rows($result);
	while($row = mysqli_fetch_array($result))  
	{
		$userid=$row['user_id'];
		$fname=$row['fname'];
	}
	$_SESSION['user']=$userid;
	$_SESSION['username']=$fname;
	
	if($count==1){
				/*$query="SELECT * FROM attend WHERE idStudent=$idStudent"; 
				$res=mysqli_query($dbC,$query);
				$countlesson=mysqli_num_rows($res);
				//echo $countlesson;
				if($countlesson!=0){
					// o kathigitis exei mathima kai meta elegxos an mono ena mathima kateutheian index else sto mathimata.php	
					//if($countlesson>1)
					//{	*/
						header("location:index2");
					/*//}else{
					//	header("location:mathimata.php");
					//}
				}
			//}*/
	}else{
		header("location:index.php?error=true");
	}
}else {
		header("location:index.php?empty=true");
}

global $username;

?>