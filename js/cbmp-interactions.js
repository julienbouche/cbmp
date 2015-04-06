
var cbmp = cbmp || {};
cbmp.draw;

cbmp.interactions = {
    addDrawInteraction : function (map) {
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
    
    generateDrawControl : function(map, opt_options){
            var options = opt_options || {};
            var draw_enabled = false;

            var anchor = document.createElement('button');
            anchor.href = '#export-geojson';
            anchor.innerHTML = 'P';
            anchor.title = "Add places on the map";
    
            var this_ = this;
            var getGeoJSON = function(e) {
                if (!draw_enabled) {
                    //code  
                    cbmp.interactions.addDrawInteraction(carte.getMap());
                }
                else {
                    //remove draw
                    cbmp.interactions.removeDrawInteraction(carte.getMap());
                }
                draw_enabled = !draw_enabled;

            };
    
            anchor.addEventListener('click', getGeoJSON, false);
            anchor.addEventListener('touchstart', getGeoJSON, false);
    
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
    
    removeDrawInteraction : function(map){
        map.removeInteraction(cbmp.draw);
    }
};