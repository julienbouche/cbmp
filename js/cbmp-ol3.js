var cbmp = {
    //constructor
    CBMap : function(containerId){
        var container = containerId;
        var vectorSource, map, popup;
        
        this.hidePopup = function(){
            if (popup) {
                //code
                popup.hide();
            }
        }
        
        //accesser
        this.getMap = function(){
            return map;
        };
        
        
        this.init = function() {
        
            //create layer to add places on
            vectorSource = new ol.source.Vector({
                    features: [],
                    projection:"EPSG:3857"
                    });
                
            var breweriesLayer = new ol.layer.Vector({
                source: vectorSource
                });
            
            //create the view
            var myView =new ol.View({
                center: ol.proj.transform([2.34, 48.82], 'EPSG:4326', 'EPSG:3857'), 
                zoom: 5
            });
            
            //defines the map
            map = new ol.Map({
                target: container,
                layers: [
                    new ol.layer.Tile({
                        source: new ol.source.MapQuest({layer: 'osm'})
                    }),
                    breweriesLayer
                ],
                view:myView
            });
        
            var element = document.getElementById('popup');
        
            //add an element to manage information popups     
            popup = new ol.Overlay.Popup({
                element: element,
                positioning: 'bottom-center',
                stopEvent: false
            });
            map.addOverlay(popup);
            
            //add an event to trap clicking elements
            map.on('click', function(evt) {
                //retrieve element that has been clicked
                var feature = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                    return feature;
                });
                
                if (feature) { //if element exists, show infos from it
                    
                    //get the position
                    var geometry = feature.getGeometry();
                    var coord = geometry.getCoordinates();
                    popup.setPosition(coord);

                    if (feature.get("name")) {//there is an existing place where the user clicked     
                        var infos = "";
                        infos+='<div>';
                        infos+='<h2>'+feature.get('name')+'</h2>';
                        
                        if ( feature.get('website')!='') {
                            infos+='<a href="'+feature.get('website')+'" target="_blank"><img src="img/gemicon/website32x32.png" height="20" width="20"/></a>';
                        }
                        
                        if ( feature.get('facebook')!='') {
                            infos+='<a href="'+feature.get('facebook')+'"><img src="img/gemicon/fb32.png" height="20" width="20"/></a>';
                        }
                        
                        if ( feature.get('twitter')!='') {
                            infos+='<a href="'+feature.get('twitter')+'"><img src="img/gemicon/twitter32.png" height="20" width="20"/></a>';
                        }
                        /*
                        */
                        
                        infos+='<p id="desc'+feature.get('id')+'">'+feature.get('desc')+'</p>';
                        infos+='<input type="button" value="+" onclick="getDescriptionDetails(this,'+feature.get('id')+');" />';
                        infos+='<input type="button" value="Delete" onclick="deletePlace(\''+feature.get('name')+'\','+feature.get('id')+');"/>';
                        infos+='</div>';
                    }
                    else{ //the user is trying to add a new place
                        var lng, lat, newcoord;
                        newcoord = geometry.clone().transform("EPSG:3857","EPSG:4326").getCoordinates();
                        lng = newcoord[0];
                        lat = newcoord[1];
                        
                        infos = "<form method='GET' action='ws/addPlace.php' onsubmit='return(sendFormData(this));'>";
                        infos += "<input type='text' placeholder='Name' name='name' />";
                        infos += "<input type='text' placeholder='Description' name='desc' />";
                        infos += "<input type='text' placeholder='http://' name='website' />";
                        infos += "<input type='text' placeholder='URL Facebook' name='facebook'/>";
                        infos += "<input type='text' placeholder='URL twitter' name='twitter' />";
                        infos += "<input type='hidden' name='lat' value='"+lat+"'/>";
                        infos += "<input type='hidden' name='lng' value='"+lng+"'/>";
                        infos += "<select name='type'><option value='1'>Brewery</option><option value='2'>Bar</option></select>"; //TODO retrieve data from DB
                        infos += "<input type='submit' value='OK' />";
                        infos += "</form>";
                    }
                    
                    //show the informations
                    popup.show(coord, infos);
                }
                else{
                    //hide potentially previously displayed popup
                    popup.hide();
                }
            });
        };
        
        this.load_places = function (url) {
            xhr = createXHR();
            if(xhr!=null) {
                xhr.open("GET",url, true);
                xhr.onreadystatechange = function(){
                    if ( xhr.readyState == 4 ){
                        
                        //delete all previous items in the layer
                        vectorSource.clear();
                        
                        //retrieve items
                        var jsonPlaces = eval(xhr.responseText);
                        var olplaces = [];
                        
                        //styling point for brewery
                        var iconStyle = new ol.style.Style({
                            image: new ol.style.Icon(
                            ({
                                anchor: [0.5, 0.5],
                                anchorXUnits: 'fraction',
                                anchorYUnits: 'fraction',
                                opacity: 1,
                                src: 'img/beerbarrel.svg',
                                scale:0.5
                            }))
                        });
                        
                        //styling point for bar/brewpub
                        var iconStyle2 = new ol.style.Style({
                            image: new ol.style.Icon(
                            ({
                                anchor: [0.4, 20],
                                anchorXUnits: 'fraction',
                                anchorYUnits: 'pixels',
                                opacity: 1,
                                src: 'img/beer.png',
                                scale:0.3
                            }))
                        });
        
                        var olplace;
                        for(var indexPlaces=0;indexPlaces<jsonPlaces.length; indexPlaces++){
                           
                            //creating the point
                            olplace = new ol.Feature({
                                geometry : new ol.geom.Point([jsonPlaces[indexPlaces].lng/1000000,jsonPlaces[indexPlaces].lat/1000000]).transform('EPSG:4326', 'EPSG:3857'),
                                name : jsonPlaces[indexPlaces].name,
                                id : jsonPlaces[indexPlaces].id,
                                desc : jsonPlaces[indexPlaces].desc,
                                type : jsonPlaces[indexPlaces].type,
                                website : jsonPlaces[indexPlaces].website,
                                facebook : jsonPlaces[indexPlaces].facebook,
                                twitter: jsonPlaces[indexPlaces].twitter
                            });
                            
                            //styling the marker's place
                            if(jsonPlaces[indexPlaces].type === "Brewery"){
                                olplace.setStyle(iconStyle);
                            }
                            else{
                                olplace.setStyle(iconStyle2);
                            }
                            
                            //adding the place to the list
                            olplaces[indexPlaces] = olplace;
                        }
                        
                        vectorSource.addFeatures(olplaces);
                    }
                };
                xhr.send(null);		
            }
        }
    }
};

