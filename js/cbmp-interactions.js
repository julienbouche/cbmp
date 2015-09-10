
var cbmp = cbmp || {};
cbmp.draw;
cbmp.geoloctracking;

cbmp.interactions = {
    enableDrawInteraction : function (map) {
        if (map) {
            if (!cbmp.draw) {
                //code
                var source;
                cbmp.draw = new ol.interaction.Draw({
                    source: source,
                    type: 'Point'
                });
            }
            map.addInteraction(cbmp.draw);
        }
    },
    
    disableDrawInteraction : function(map){
        map.removeInteraction(cbmp.draw);
    },
    
    generateDrawControl : function(map, opt_options){
            var options = opt_options || {};
            var draw_enabled = false;
    
            var switchDrawInteraction = function(e) {
                if (!draw_enabled) {
                    //code  
                    cbmp.interactions.enableDrawInteraction(carte.getMap());
                }
                else {
                    //remove draw
                    cbmp.interactions.disableDrawInteraction(carte.getMap());
                }
                draw_enabled = !draw_enabled;

            };
    
            var controlContainerDOM = cbmp.interactions.createCustomControl('#addPlace', 'P', "Add places on the map", switchDrawInteraction);
            
            ol.control.Control.call(this, {
              element: controlContainerDOM,
              options: options
            });
            
            
            
    },
    
    createCustomControl : function (href, text, title, clickCallback){
        var anchor = document.createElement('button');
        anchor.href = href;
        anchor.innerHTML = text;
        anchor.title = title;
        
        anchor.addEventListener('click', clickCallback, false);
        anchor.addEventListener('touchstart', clickCallback, false);
        
        var controlContainerDOM = document.getElementById('cmbpControlContainer');
        
        if (!controlContainerDOM) {
            //create the container
            controlContainerDOM = document.createElement('div');
            controlContainerDOM.id = "cmbpControlContainer";
            controlContainerDOM.className = 'cbmp-button ol-control ol-unselectable';
        }
        
        //adds the new control button to the container
        controlContainerDOM.appendChild(anchor);
        
        return controlContainerDOM;
    },
    
    addDrawControl : function(map){
        ol.inherits(cbmp.interactions.generateDrawControl, ol.control.Control);
        map.addControl(new cbmp.interactions.generateDrawControl());
    },
    
    
    initGeoLocTrackingControl : function(activateCallback, deactivateCallback){
        var geoloc_enabled = false;

        var switchGeoLocTrackingStateInteraction = function(e) {
            if (!geoloc_enabled) {
                //calls the activation function callback
                activateCallback();
            }
            else {
                //calls the deactivate function callback
                deactivateCallback();
            }
            geoloc_enabled = !geoloc_enabled;

        };
        
        var controlContainerDOM = cbmp.interactions.createCustomControl('#trackme', 'T', "Track me!", switchGeoLocTrackingStateInteraction);
        ol.control.Control.call(this, {
              element: controlContainerDOM
            });

    },
    
    addGeoLocTrackingControl : function(map){
        ol.inherits(cbmp.interactions.initGeoLocTrackingControl, ol.control.Control);
        map.addControl(new cbmp.interactions.initGeoLocTrackingControl(carte.activateGeoLocationTracking, carte.deactivateGeoLocationTracking));
    },
    disableGeoLocTracking : function (map){
        cbmp.removeInteraction(cbmp.geoloctracking);
    },
    enableGeoLocTracking : function(map){
        if (map) {
            if (!cbmp.geoloctracking) {
                //code
                var source;
                cbmp.geoloctracking = new ol.interaction.Draw({
                    source: source,
                    type: 'Point'
                });
            }
            map.addInteraction(cbmp.geoloctracking);
        }
    }
};