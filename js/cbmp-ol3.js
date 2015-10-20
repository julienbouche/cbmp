var cbmp = {
    /**
     * Definition of the project's Map class
     */
    CBMap : function(containerId){
        var _this = this;
        var container = containerId;
        var vectorsSource, map, myView, popup, clusters;
        var geoLocationTrackingEnabled = false;
        var geolocation;
        var zoomOnFirstTracking=true;
        var NB_PLACES_CATEGORY;
        
        /**
         * Function to hide the popup
         */
        this.hidePopup = function(){
            if (popup) {
                //code
                popup.hide();
            }
        }
        
        /**
         * Function to fill popup inner HTML content
         * @param {String} html the html code to be placed in the form inner HTML
         */
        this.setPopupContent = function(html){
            if (popup) {
                //code
                popup.content.innerHTML = html;
            }
        }
        
        /**
         * Simple accesser that return the map object
         * @returns {ol.Map} the object representing the map
         */
        this.getMap = function(){
            return map;
        };
        
        /**
         * Function to adapt zoom and center of the view to the given parameters
         * @param {Integer} ll latitude
         * @param {Integer} lg longitude
         * @param {Integer} zoom zoom level
         */
        this.adjustView= function(ll, lg, zoom){
            //set zoom
            myView.setZoom(zoom);
            
            //centers map
            myView.setCenter([lg,ll]);
                
        };
        
        /**
         * Function to turn on geolocation tracking
         */
        this.activateGeoLocationTracking = function(){
            geoLocationTrackingEnabled=true;
            if(geolocation){
                geolocation.setTracking(geoLocationTrackingEnabled);
            }
            
        };
        
        /**
         * Function to turn off geolocation tracking
         */
        this.deactivateGeoLocationTracking = function(){
            geoLocationTrackingEnabled=false;
            if(geolocation) {
                geolocation.setTracking(geoLocationTrackingEnabled);
            }
            
            //reinit zoom at first geolocation 
            zoomOnFirstTracking=true;
        };
        
        
        /**
         * Function that initialize the geolocation feature
         * @param {ol.View} myView ol objects that represent the view
         */
        this.initGeoLocationFeature = function(myView){
            //add geolocation possibility
            geolocation = new ol.Geolocation({
                projection: myView.getProjection()
            });
            
            geolocation.setTracking(geoLocationTrackingEnabled);
            
            //create a feature representing location's accuracy
            var accuracyFeature = new ol.Feature();
            geolocation.on('change:accuracyGeometry', function() {
                accuracyFeature.setGeometry(geolocation.getAccuracyGeometry());
            });

            //create a feature for the user's position
            var positionFeature = new ol.Feature();
            positionFeature.setStyle(new ol.style.Style({
                image: new ol.style.Circle({
                radius: 6,
                fill: new ol.style.Fill({
                  color: '#3399CC'
                }),
                stroke: new ol.style.Stroke({
                  color: '#fff',
                  width: 2
                    })
                })
            }));
            
            //handles errors
            geolocation.on('error', function(error) {
                alert(error.message);
            });
            
            
            //handles position changes (recenter map on new position)
            geolocation.on('change:position', function() {
                var coordinates = geolocation.getPosition();
                var geomPoint = new ol.geom.Point(coordinates);
                positionFeature.setGeometry(coordinates ? geomPoint : null);
                
                //center the view on user's location
                myView.setCenter(coordinates);
                
                //zoom only the first position (enables the user to adjust zoom)
                if (zoomOnFirstTracking) {
                    //zoom in
                    myView.setZoom(10);
                    zoomOnFirstTracking=false;
                }
            });

            //adds a new vector with position and accuracy
            var geolocFeatureOverlay = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: [accuracyFeature, positionFeature]
                })
            });
            return geolocFeatureOverlay;
        }
        
        
        /**
         * function that loads categories from the backend in json format (ws/getCategories.php)
         * @param {String} menu_dom_element_id the id of the ul element to loads the menu
         * @param {Function} interactionsCallbackFunction function to be called after categories have been retrieved
         */
        this.load_categories =function(menu_dom_element_id, interactionsCallbackFunction){
            var url = "ws/getCategories.php";
            var xhr = createXHR();
            if(xhr!=null) {
                //defines an asyncrhonous call to the URL url using GET method
                xhr.open("GET",url, true);
                
                
                xhr.onreadystatechange = function(){ //executed after AJAX response returned
                    if ( xhr.readyState == 4 ){
                        var jsonCategories = eval(xhr.responseText);
                        
                        //stores the number of places retrieved
                        NB_PLACES_CATEGORY = jsonCategories.length;
                        
                        //generates the menu
                        cbmp.interactions.generateLayersMenu(jsonCategories, menu_dom_element_id);
                        
                        //calls the callback functions after retrieving elements
                        interactionsCallbackFunction();
                    }
                };
                xhr.send(null);
            }
        };
        
        /**
         * Overall init function
         * @param {String} menu_dom_element_id the id of the ul element to loads the menu
         * @param {Function} interactionsCallbackFunction function to be called after required initialisation
         */
        this.init = function(menu_dom_element_id, interactionsCallbackFunction){
            //this.load_categories(menu_dom_element_id, );
            this.load_categories(menu_dom_element_id, function(){
                        //launch initialisation of map elements
                        _this.initLayers();
                        
                        //loads places 
                        _this.load_places("ws/getPlaces.php");
                        
                        //calls the callback function
                        interactionsCallbackFunction();
            })
        };
        
        
        /**
         * Internal init function that initialise layers, map options, clustering strategy, etc.
         */
        this.initLayers = function() {
            //create layer to add places on
            vectorsSource = {};
            placesLayers = {};
            clusters = [];
            var styleCache = {};
            
            //loop for each type of place
            for(index=0; index < NB_PLACES_CATEGORY; index++){
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
            myView =new ol.View({
                center: ol.proj.transform([2.34, 48.82], 'EPSG:4326', 'EPSG:3857'), 
                zoom: 5
            });
            
            //enables geolocation tracking
            var geolocFeatureOverlay = _this.initGeoLocationFeature(myView);
            
            
            //defines the map
            map = new ol.Map({
                target: container,
                layers: [
                    new ol.layer.Tile({
                        source: new ol.source.MapQuest({layer: 'osm'})
                    }),
                    geolocFeatureOverlay 
                ],
                view:myView
            });
            
            //for each cluster, add it to the map
            for (var index=0; index<NB_PLACES_CATEGORY; index++) {
                //code
                map.addLayer(clusters[index]);
            }
            
        
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
        
        
        /**
         * Function to load the places from the backend page in JSON format
         * @param {String} url the url from where to load places
         */
        this.load_places = function (url) {
            xhr = createXHR();
            if(xhr!=null) {
                xhr.open("GET",url, true);
                xhr.onreadystatechange = function(){
                    if ( xhr.readyState == 4 ){
                        
                        //delete all previous items in each layer
                        for(var i = 0; i<NB_PLACES_CATEGORY; i++)vectorsSource[i].clear();
                        
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

