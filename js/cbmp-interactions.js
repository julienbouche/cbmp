
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

            var anchor = document.createElement('button');
            anchor.href = '#addPlace';
            anchor.innerHTML = 'P';
            anchor.title = "Add places on the map";
    
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
    
            anchor.addEventListener('click', switchDrawInteraction, false);
            anchor.addEventListener('touchstart', switchDrawInteraction, false);
    
            var element = document.createElement('div');
            element.className = 'cbmp-button ol-control ol-unselectable';
            element.appendChild(anchor);
    
            ol.control.Control.call(this, {
              element: element,
              target: options.target
            });
    },
    
    addDrawControl : function(map){
        ol.inherits(cbmp.interactions.generateDrawControl, ol.control.Control);
        map.addControl(new cbmp.interactions.generateDrawControl());
    },
    
    
    initGeoLocTrackingControl : function(activateCallback, deactivateCallback){
        var geoloc_enabled = false;

        var anchor = document.createElement('button');
        anchor.href = '#trackme';
        anchor.innerHTML = 'T';
        anchor.title = "Track me!";

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

        anchor.addEventListener('click', switchGeoLocTrackingStateInteraction, false);
        anchor.addEventListener('touchstart', switchGeoLocTrackingStateInteraction, false);

        var element = document.createElement('div');
        element.className = 'cbmp-button ol-control ol-unselectable';
        element.appendChild(anchor);

        ol.control.Control.call(this, {
          element: element
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