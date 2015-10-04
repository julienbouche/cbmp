
var cbmp = cbmp || {};
cbmp.draw;
cbmp.geoloctracking;

/**
 * Library of function to activate interactions
 */
cbmp.interactions = {
    
    /**
     * Function to turn on the functionnality to add new places on the map
     * @param {ol.Map} map the openlayers object representing the map
     */
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
    
    /**
     * Function to turn off the functionnality to add a new place
     * @param {ol.Map} map the openlayers object representing the map
     */
    disableDrawInteraction : function(map){
        map.removeInteraction(cbmp.draw);
    },
    
    /**
     * Function to add a control to turn on/off the functionnality to add new places on the map
     * @param {ol.Map} map the openlayers object representing the map
     * @param {} opt_options the options to add to the creation of the control object
     */
    generateDrawControl : function(map, opt_options){
            var options = opt_options || {};
            var draw_enabled = false;
    
            var switchDrawInteraction = function(e) {
                if (!draw_enabled) {
                    //enable the creation of new places 
                    cbmp.interactions.enableDrawInteraction(map);
                }
                else {
                    //disable the creation of new places
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
    
    /**
     * Function that adds an entry to the menu allowing to select which layer to show
     * @param {String} name the display name of the layer
     */
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
    
    /**
     * Function to create the menu of layer selector based on an array
     * @param {Array} jsonCategories list of all categories
     * @param {String} menu_dom_element_id id of the ul container element
     */
    generateLayersMenu : function(jsonCategories, menu_dom_element_id){
        var menu_dom_element = document.getElementById(menu_dom_element_id);
        
        for(var index=0; index<jsonCategories.length; index++){
            //create an html element for that category
            var new_selector = cbmp.interactions.createDOMLayerSelector(jsonCategories[index].name);
            
            //adds it to the container
            menu_dom_element.appendChild(new_selector);
            
        }
    },
    
    /**
     * Generic function to add a user control
     * @param {String} href
     * @param {String} text
     * @param {String} title
     * @param {Function} clickCallback function to be called when user interacts with this control
     */
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
    
    
    /**
     * Function to add a user control to enable/disable the functionnality to add a new place
     */
    addDrawControl : function(map){
        ol.inherits(cbmp.interactions.generateDrawControl, ol.control.Control);
        map.addControl(new cbmp.interactions.generateDrawControl(map));
    },
    
    
    /**
     * Function to initialize user control to enable/disable geolocation tracking
     * @param {Function} activateCallback function to be called on enabling functionnality
     * @param {Function} deactivateCallback function to be called on disabling functionnality
     */
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
    
    
    /**
     * Function to add the user control and initialize all required UI objects
     * @param {ol.Map} map the openlayers object representing the map
     */
    addGeoLocTrackingControl : function(map){
        ol.inherits(cbmp.interactions.initGeoLocTrackingControl, ol.control.Control);
        map.addControl(new cbmp.interactions.initGeoLocTrackingControl(carte.activateGeoLocationTracking, carte.deactivateGeoLocationTracking));
    }
};