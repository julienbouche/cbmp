	function filterlist(filter, list, nbCharMin){
		var filterNbCharMin = nbCharMin || 3;
		
		var li_items = list.getElementsByTagName("li");
		
		if(filter.length> filterNbCharMin){
			//retrieve Places with filter contained in name
			getPlacesFiltered(filter, list);
		}
		else{
			//hide list
			disableAutoCompleteNow();
		}
	}
	
	//creates AJAX Call to get places and populate ul element to display results
	function getPlacesFiltered(filter, list) {
		
		var places = [];
		var xhr = createXHR();
		var data = "q="+filter;
		
		xhr.open("GET", "ws/getPlaces.php?"+data);
		
		
		xhr.onreadystatechange = function(){
                    if ( xhr.readyState == 4 ){
			
			//evaluate the JSON response
			var places = eval(xhr.responseText);

			//empty list
			list.innerHTML = "";
			
			//populate list with items retrieved
			for(var indexPlaces=0;indexPlaces<places.length;indexPlaces++){
				var li_elt = document.createElement("li");
				
				li_elt.innerHTML = places[indexPlaces].name;
				li_elt.lng = places[indexPlaces].lng/1000000;
				li_elt.lat =places[indexPlaces].lat/1000000
				
				li_elt.onclick=function(){
					var map = carte.getMap();
					map.getView().setCenter(ol.proj.transform([li_elt.lng, li_elt.lat], 'EPSG:4326', 'EPSG:3857'));
					map.getView().setZoom(15);
				};
				list.appendChild(li_elt);
			}
			
			//display list
			activateAutoComplete();
		    }
		};
		
		xhr.send(null);
	}
	
	function selectItem(li_element, input_dom){
		input_dom.value = li_element.innerHTML;
	}
	
	function activateAutoComplete(){
		document.getElementById("ac_container").style.display="block";
	}
	
	function disableAutoComplete(){
		setTimeout(disableAutoCompleteNow, 300);		
	}
	
	function disableAutoCompleteNow(){
		document.getElementById("ac_container").style.display="none";
	}
	
	function captureKeyEvents(elt, event){
		var li_items = document.getElementsByTagName("li");
		var displayed_li_items = new Array();
		var cpt_displayed_li_items = 0;
		
		//get all displayed elements
		for(var i=0; i<li_items.length; i++){
			if(li_items[i].style.display != "none"){
				displayed_li_items[cpt_displayed_li_items++] = li_items[i];
			}
		}
		
		switch(event.keyCode){
			case 9 : //tab
				//validate the selected item displayed
				var selected = findSelectedItem(displayed_li_items);
				if(selected>=0){
					selectItem(displayed_li_items[selected], elt);
				}
				break;
			case 38: //arrow_up
				//changes the selected item to the previous one in the list
				selectPreviousItem(displayed_li_items);
				break;
			case 40: //arrow_down
				//changes the selected item to the next one in the list
				selectNextItem(displayed_li_items);
				break;
		}
	}
	
	function selectNextItem(li_items){
		var selectedItem_idx=-1;
		
		//recherche élément sélectionné
		selectedItem_idx = findSelectedItem(li_items);
		
		if(selectedItem_idx>=0 && selectedItem_idx+1<li_items.length){
			
			li_items[selectedItem_idx+1].className+=" selected";
			
			//déselection de l'ancien élément sélectionné
			li_items[selectedItem_idx].className="";
		}
		else{
			if(selectedItem_idx==-1){
				li_items[0].className+=" selected";
			}
		}
	}
	
	function selectPreviousItem(li_items){
		var selectedItem_idx=-1;
		
		//recherche élément sélectionné
		selectedItem_idx = findSelectedItem(li_items);
		
		if(selectedItem_idx-1>=0 && selectedItem_idx<li_items.length){
			
			li_items[selectedItem_idx-1].className+=" selected";
			
			//déselection de l'ancien élément sélectionné
			li_items[selectedItem_idx].className="";
		}
		else{
			if(selectedItem_idx==-1){
				li_items[li_items.length].className+=" selected";
			}
		}
	}
	
	function findSelectedItem(li_items){
		for(var i=0; i<li_items.length; i++){
			if( li_items[i].className.indexOf("selected")>=0){
				return i;
			}
		}
		return -1;
	}