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
                
            var breweriesLayer = new ol.source.Cluster({
                distance:30,
                source: vectorSource
                });
            
            //create the view
            var myView =new ol.View({
                center: ol.proj.transform([2.34, 48.82], 'EPSG:4326', 'EPSG:3857'), 
                zoom: 5
            });
            
            
            var styleCache = {};
            var clusters = new ol.layer.Vector({
                source: breweriesLayer,
                style: function(feature, resolution) {
                  var size = feature.get('features').length;
                  var style = styleCache[size];
                  if (!style) {
                    if (size==1) {
                        //Only One element to show
                        style = [new ol.style.Style({
                            image: new ol.style.Icon(
                            ({
                                anchor: [0.5, 0.5],
                                anchorXUnits: 'fraction',
                                anchorYUnits: 'fraction',
                                opacity: 1,
                                src: 'img/beerbarrel.svg',
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
                        //Clustering strategy for multiple elements
                        style = [new ol.style.Style({
                      image: new ol.style.Circle({
                        radius: 10,
                        stroke: new ol.style.Stroke({
                          color: '#fff'
                        }),
                        fill: new ol.style.Fill({
                          color: '#FFCC00'
                        })
                      }),
                      text: new ol.style.Text({
                        text: size.toString(),
                        fill: new ol.style.Fill({
                          color: '#fff'
                        })
                      })
                    })];
                    }
                    
                    styleCache[size] = style;
                  }
                  return style;
                }
              });
            
            //defines the map
            map = new ol.Map({
                target: container,
                layers: [
                    new ol.layer.Tile({
                        source: new ol.source.MapQuest({layer: 'osm'})
                    }),
                    clusters
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
                                id : jsonPlaces[indexPlaces].id
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

