using UnityEngine;
using System.Collections;

public class ObjectTap : MonoBehaviour {

	string name;
	//public GUISkin toggleSkin;
	Vector2 xy;
	bool showbox = false;
	private GUISkin hotCitySkin;
	//DownloadData d;
	// Use this for initialization
	void Start () {
		hotCitySkin = (GUISkin)Resources.Load ("hotCitySkin");
	}
	
	// Update is called once per frame
	void Update () {

		foreach (Touch touch in Input.touches) {
			if (touch.phase == TouchPhase.Began) {
				Ray ray = Camera.main.ScreenPointToRay (Input.GetTouch (0).position);
				RaycastHit hit;
				//5000 η μεγιστη αποσταση της ακτινας απο το touch (οσο μεγαλυτερο τοσο καλυτερα για την ανιχνευση του touch πανω στο 3d αντικειμενο)
				if (GetComponent<Collider>().Raycast (ray, out hit, 5000.0f)) {
					showbox = true;
					name = hit.transform.name;
				} else {
						//Debug.Log ("Hit detected not on object at point " + hit.point);
						//StartCoroutine(send ("Nothing selected","",""));
						showbox = false;
				}
			}	
		}
	}
	private GUIStyle poilabelStyle;
	void OnGUI()
    {
		GUI.skin = hotCitySkin;
		poilabelStyle  =  hotCitySkin.GetStyle("poilabel");
		if(showbox){
			//GUI.skin.label.fontSize = 50;
			Vector2 width = GUI.skin.label.CalcSize(new GUIContent(name));
			//float height = GUI.skin.label.CalcHeight(new GUIContent(name),400);
			//GUI.Box(new Rect(Screen.width/2, Screen.height/2,width.x+20, 30), "");
			GUI.Label(new Rect(Screen.width/2, Screen.height/2,width.x,150),name,poilabelStyle);
		}
	}
}
