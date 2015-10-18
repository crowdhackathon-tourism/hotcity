using UnityEngine;
using System.Collections;

public class LocationService : MonoBehaviour {

	private Vector2 nowloc;
	private Vector2 prevloc = new Vector2(0,0);
	private static Vector2 destination = new Vector2(0,0);//38.246227,21.735064
	private float now = 0;
	private float prevtime = 0;
	private float avgspeed = 5;
	private LocationInfo gpsPos;
	private bool locFound = false;
	private double mLat;
	private double rLat;
	private double mLon;
	private double rLon;
	private GameObject loc;
	private GameObject city;
	private LineRenderer lineRenderer;
	public GUISkin hotCitySkin;
	// Use this for initialization
	IEnumerator Start()
	{
		hotCitySkin = (GUISkin) Resources.Load ("hotCitySkin");
		initializeLocationGUI ();
		city = GameObject.Find("ImageTarget");
		// First, check if user has location service enabled
		if (!Input.location.isEnabledByUser)
			yield break;
		Input.location.Start(1f, 5f);
		getLocation();
		InvokeRepeating("speed",0,10);
	}

	IEnumerator getLocation(){		
		// Wait until service initializes
		int maxWait = 20;
		while (Input.location.status == LocationServiceStatus.Initializing && maxWait > 0)
		{
			yield return new WaitForSeconds(1);
			maxWait--;
		}
		
		// Service didn't initialize in 20 seconds
		if (maxWait < 1)
		{
			Debug.Log("Timed out");
			yield break;
		}
		
		// Connection has failed
		if (Input.location.status == LocationServiceStatus.Failed)
		{
			print("Unable to determine device location");
			yield break;
		}
		else
		{
			locFound = true;
			// Access granted and location value could be retrieved
			Debug.Log("Location: " + Input.location.lastData.latitude + " " + Input.location.lastData.longitude + " " + Input.location.lastData.altitude + " " + Input.location.lastData.horizontalAccuracy + " " + Input.location.lastData.timestamp);
		}
		gpsPos = new LocationInfo();
		// Stop service if there is no need to query location updates continuously
		//Input.location.Stop();
	}

	public static void setDestination(Vector2 destinationroute){
		destination = destinationroute;
	}
	
	// Update is called once per frame
	void Update () {
		gpsPos = Input.location.lastData;
		nowloc = new Vector2 (gpsPos.latitude, gpsPos.longitude);
		Vector2 currentLoc = new Vector2(gpsPos.latitude, gpsPos.longitude);		
		Vector2 vectorToDest = destination - currentLoc;
		Vector3 vectorToDest3D = new Vector3(vectorToDest.x, 0, vectorToDest.y);
		
		loc.transform.LookAt(loc.transform.position + vectorToDest3D*1000);
		//loc.transform.Rotate(Vector3.up * -Input.compass.magneticHeading);
	}

	void initializeLocationGUI()
	{  	
		loc = Instantiate(Resources.Load("locarrow"), new Vector3 (0,3,0), Quaternion.identity) as GameObject;
		loc.transform.parent = city.transform;
		
		//lineRenderer = city.GetComponent(LineRenderer);
		//lineRenderer.material = new Material (Shader.Find("Particles/Additive"));
		//lineRenderer.transform.parent = city.transform;

	}

	void speed()
	{		
		prevtime = now;
		now = Time.time+1;
		prevloc = nowloc;
		nowloc = new Vector2(gpsPos.latitude, gpsPos.longitude);
		
		float dist = CalcDistance(nowloc.x,nowloc.y,prevloc.x,prevloc.y);
		
		var timeInHours = (now - prevtime)/3600; //now - prevtime = 10 always -> 0.00277 hours
		float speed = dist/ timeInHours;
		
		if(speed < 1)
			speed = 5;//average human walking speed 
		
		avgspeed = speed;
	}

	float CalcDistance(float lon1, float lat1, float lon2, float lat2)
	{
		var R = 6371; // radius of earth in km
		var dLat = (lat2-lat1)*(Mathf.PI / 180);
		var dLon = (lon2-lon1)*(Mathf.PI / 180);
		lat1 = lat1*(Mathf.PI / 180);
		lat2 = lat2*(Mathf.PI / 180);
		
		var a = Mathf.Sin(dLat/2) * Mathf.Sin(dLat/2) + Mathf.Sin(dLon/2) * Mathf.Sin(dLon/2) * Mathf.Cos(lat1) * Mathf.Cos(lat2); 
		var c = 2 * Mathf.Atan2(Mathf.Sqrt(a), Mathf.Sqrt(1-a)); 
		var d = R * c;
		
		return d;
	}

	void OnGUI() {
		GUI.skin = hotCitySkin;
		
		//GUI.skin = toggleSkin;
		GUI.Box(new Rect(Screen.width / 2-300,Screen.height - 200,600,195), "");
		float dist = CalcDistance(gpsPos.longitude, gpsPos.latitude, destination.y, destination.x);
		//GUI.Label(new Rect(Screen.width / 2-290,Screen.height - 190, 550,100),"Destination: ");//distname
		if(dist<1)//if distance is less than 1km convert to meters
		{
			float tempdist = dist* 1000;
			GUI.Label(new Rect(Screen.width / 2-290,Screen.height - 65, 550,50),"Distance: "+ tempdist.ToString("F2")+" m");
		}
		else
			GUI.Label(new Rect(Screen.width / 2-290,Screen.height - 65, 550,50),"Distance: "+ dist.ToString("F2")+" km");
		
		float destTime = dist/avgspeed;
		if(destTime<1) //if estimated time is less than 1 hour convert to minutes
		{
			float temptime = destTime * 60;
			GUI.Label(new Rect(Screen.width / 2-290,Screen.height - 120, 550,50),"ETA: "+temptime.ToString("F1")+" mins");
		}
		else
			GUI.Label(new Rect(Screen.width / 2-290,Screen.height - 120, 550,50),"ETA: "+destTime.ToString("F1")+" hours");
		
		/*if(locFound){
			double tmpLat = gpsPos.latitude - mLat;//gpsPos.latitude
			float z = (71.3 * tmpLat) / rLat;
			double tmpLon = gpsPos.longitude - mLon;//gpsPos.longitude
			float x = (100 * tmpLon) / rLon;
			
			//loc.transform.position = Vector3 (x,2,z);
		}else
			GUI.Label(Rect(Screen.width / 2, Screen.height / 2, 250, 100), "Your location is unavailable.");
		*/
		if(GUI.Button (new Rect (Screen.width/2+250,10,150,80), "My Location"))//Screen.width - 100
			getLocation();
	}
}