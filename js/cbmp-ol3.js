var cbmp = {
    //constructor
    CBMap : function(containerId){
        var container = containerId;
        var vectorsSource, map, popup, clusters;
        
        this.hidePopup = function(){
            if (popup) {
                //code
                popup.hide();
            }
        }
        this.setPopupContent = function(html){
            if (popup) {
                //code
                popup.content.innerHTML = html;
            }
        }
        //accesser
        this.getMap = function(){
            return map;
        };
        
        
        this.init = function() {
        
            //create layer to add places on
            vectorsSource = {};
            placesLayers = {};
            clusters = [];
            var styleCache = {};
            
            //loop for each type of place
            for(index=0; index < 4; index++){
                //create a vector for each type
                vectorsSource[index] = new ol.source.Vector({
                    features: [],
                    projection:"EPSG:3857"
                    });
                
                //creates the corresponding layer for each type of place
                placesLayers[index] = new ol.source.Cluster({
                    distance:30,
                    source: vectorsSource[index]
                });
                
                clusters[index] = new ol.layer.Vector({
                    source: placesLayers[index],
                    style: function(feature, resolution) {
                        var size = feature.get('features').length;
                        var style = styleCache[size];  
                      
                        if (size==1) {
                            //Only One element to show
                            var icon_name = feature.get('features')[0].get('type');
                            
                            style = [new ol.style.Style({
                                image: new ol.style.Icon(
                                ({
                                    anchor: [0.5, 0.5],
                                    anchorXUnits: 'fraction',
                                    anchorYUnits: 'fraction',
                                    opacity: 1,
                                    src: 'img/'+icon_name+'.png',
                                    scale:0.5
                                })),
                                text: new ol.style.Text({
                                    text: '',
                                    fill: new ol.style.Fill({
                                        color: '#fff'
                                    })
                                })
                            })];
                            
                        }
                        else {
                            if (!style) {
                                //Clustering strategy for multiple elements
                                style = [new ol.style.Style({
                                    image: new ol.style.Circle({
                                        radius: 10,
                                        stroke: new ol.style.Stroke({color: '#fff'}),
                                        fill: new ol.style.Fill({color: '#FFCC00'})
                                        }),
                                    text: new ol.style.Text({
                                        text: size.toString(),
                                        fill: new ol.style.Fill({color: '#fff'})
                                    })
                                })];
                                styleCache[size] = style;
                            }
                      }
                      return style;
                    }
              });
            }
            
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
                    clusters[0], clusters[1], clusters[2],clusters[3]
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
                    
                    if (feature.get('features') && feature.get('features').length==1 && feature.get('features')[0].get("name")) {//there is an existing place where the user clicked     
                        var selectedFeature = feature.get('features')[0];
                        //load informations from URL
                        xhr = createXHR();
                        if(xhr!=null) {
                            xhr.open("GET","displayPlaceInfos.php?id="+selectedFeature.get('id'), true);
                            xhr.onreadystatechange = function(){
                                popup.show(coord, xhr.responseText);
                            };
                            xhr.send(null);
                        }
                        
                    }
                    else{ //the user is trying to add a new place
                        var lng, lat, newcoord;
                        newcoord = geometry.clone().transform("EPSG:3857","EPSG:4326").getCoordinates();
                        lng = newcoord[0];
                        lat = newcoord[1];
                        xhr = createXHR();
                        if(xhr!=null) {
                            xhr.open("GET","addPlaceForm.php?lat="+lat+"&lng="+lng, true);
                            xhr.onreadystatechange = function(){
                                popup.show(coord, xhr.responseText);
                            };
                            xhr.send(null);
                        }
                    }
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
                        
                        //delete all previous items in each layer
                        for(var i = 0; i<4; i++)vectorsSource[i].clear();
                        
                        //retrieve items
                        var jsonPlaces = eval(xhr.responseText);
                        var olplaces = [];
                        var types = {};
                        var indexTypes=0;
                        var currentType;
                        
                        var olplace;
                        for(var indexPlaces=0;indexPlaces<jsonPlaces.length; indexPlaces++){
                           
                            //creating the point
                            olplace = new ol.Feature({
                                geometry : new ol.geom.Point([jsonPlaces[indexPlaces].lng/1000000,jsonPlaces[indexPlaces].lat/1000000]).transform('EPSG:4326', 'EPSG:3857'),
                                name : jsonPlaces[indexPlaces].name,
                                id : jsonPlaces[indexPlaces].id,
                                type : jsonPlaces[indexPlaces].type
                            });
                            
                            currentType = jsonPlaces[indexPlaces].type;
                            
                            //if this type doesnot already exists in the array
                            if (!(currentType in types)) {
                                //we add it and increment the index
                                types[currentType] = indexTypes++;
                                
                                //bind the dom control with this type
                                var domcontrol = document.getElementById(currentType+"DomCB");
                                if (domcontrol) {
                                    var domcontrolInput = new ol.dom.Input(domcontrol);
                                    if(domcontrolInput)domcontrolInput.bindTo('checked', clusters[types[currentType]], 'visible');
                                }
                            }
                            
                            //adding the place to the list corresponding to this type
                            vectorsSource[types[currentType]].addFeature(olplace);
                        }
                        
                        //vectorsSource[0].addFeatures(olplaces);
                    }
                };
                xhr.send(null);		
            }
        }
    }
};

