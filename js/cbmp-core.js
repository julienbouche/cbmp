function createXHR(){
	var xhr;
	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}

	//ie
	else if (window.ActiveXObject) {
		try {
			xhr = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
	} 
	return xhr;
}

function getEditForm(element,id){
	//send the delete order
		var xhr = createXHR();
		var data = "id="+id;
		
		// We define what will happen if the data are successfully sent
		xhr.addEventListener('load', function(event) {
			//if succeed, update popup's content with html response
			carte.setPopupContent(xhr.responseText);
		});
		
		// We define what will happen in case of error
		xhr.addEventListener('error', function(event) {
			alert('Oups! Something went wrong.');
		});
		
		// We setup our request
		xhr.open('GET', "editPlaceInfos.php?"+data, true);
		
		// We just send our data
		xhr.send(null);
}

function deletePlace(name, id) {
	//code
	if (confirm('Are you sure you want to delete "'+name+'"?')) {
		//hide the popup
		carte.hidePopup();
		
		//send the delete order
		var xhr = createXHR();
		var data = "id="+id;
		
		// We define what will happen if the data are successfully sent
		xhr.addEventListener('load', function(event) {
			//if succeed, refresh places displayed on the map
			carte.load_places("ws/getPlaces.php");
			carte.getMap().render();
			
		});
		
		// We define what will happen in case of error
		xhr.addEventListener('error', function(event) {
			alert('Oups! Something went wrong.');
		});
		
		// We setup our request
		xhr.open('POST', "ws/deletePlace.php", true);
		xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
		xhr.setRequestHeader('Content-length', data.length);
		xhr.setRequestHeader("Connection", "close");
		
		// We just send our data
		xhr.send(data);
	}
}

function sendFormData(form) {
	var xhr = createXHR();
	var data = "";
	
	for (var i=0; i<form.elements.length; i++) {
		//
		if (form.elements[i].type!='submit') {
			//add every data from the form as an url encoded string
			if (form.elements[i].type!='checkbox') {
				data+=form.elements[i].name+'='+encodeURIComponent(form.elements[i].value)+'&';
			}
			else {
				//element type is checkbox
				if (form.elements[i].checked) {
					data+=form.elements[i].name+'='+encodeURIComponent(form.elements[i].value)+'&';

				}
			}
			
		}
	}
	
	// We define what will happen if the data are successfully sent
	xhr.addEventListener('load', function(event) {
		//reload places
		carte.hidePopup();
		carte.load_places("ws/getPlaces.php");
		carte.getMap().render();
		
	});
	
	// We define what will happen in case of error
	xhr.addEventListener('error', function(event) {
		alert('Oups! Something went wrong.');
	});
	
	// We setup our request
	xhr.open('POST', form.action, true);
	xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	xhr.setRequestHeader('Content-length', data.length);
	xhr.setRequestHeader("Connection", "close");
	
	// We just send our data
	xhr.send(data);
	
	return false;
}