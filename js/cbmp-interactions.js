
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
                    cbmp.interactions.enableDrawInteraction(map);
                }
                else {
                    //remove draw
                    cbmp.interactions.disableDrawInteraction(map);
                }
                draw_enabled = !draw_enabled;

            };
    
            var controlContainerDOM = cbmp.interactions.createCustomControl('#addPlace', 'P', "Add places on the map", switchDrawInteraction);
            
            ol.control.Control.call(this, {
              element: controlContainerDOM,
              options: options
            });
            
            
            
    },
    
    createDOMLayerSelector: function(name){
        var new_li_element = document.createElement('li');
        
        var new_checkbox = document.createElement('input');
        new_checkbox.type='checkbox';
        new_checkbox.selected = true;
        new_checkbox.id = name+'DomCB';
        
        new_li_element.appendChild(new_checkbox);
        
        var new_label = document.createElement('label');
        new_label.innerHTML = name;
        
        new_li_element.appendChild(new_label);
        
        return new_li_element;
    },
    
    generateLayersMenu : function(jsonCategories, menu_dom_element_id){   
        var menu_dom_element = document.getElementById(menu_dom_element_id);
        
        for(var index=0; index<jsonCategories.length; index++){
            //create an entry for that category
            var new_selector = cbmp.interactions.createDOMLayerSelector(jsonCategories[index].name);
            
            //adds it to the container
            menu_dom_element.appendChild(new_selector);
            
        }
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
        map.addControl(new cbmp.interactions.generateDrawControl(map));
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
    },
    
    addDragControl : function(map){
        ol.inherits(cbmp.interactions.generateDragControl, ol.control.Control);
        map.addControl(new cbmp.interactions.generateDragControl(map));
    },
    
    generateDragControl : function(map){
        var drag_enabled = false;
    
            var switchDragInteraction = function(e) {
                if (!drag_enabled) {
                    //code  
                    cbmp.interactions.enableDragInteraction(map);
                }
                else {
                    //remove draw
                    cbmp.interactions.disableDragInteraction(map);
                }
                drag_enabled = !drag_enabled;

            };
    
            var controlContainerDOM = cbmp.interactions.createCustomControl('#drag', 'D', "Drag places", switchDragInteraction);
            
            ol.control.Control.call(this, {
              element: controlContainerDOM
            });
    }
};