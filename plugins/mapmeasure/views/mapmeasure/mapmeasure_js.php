<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* mapmeasure_js.php - Javascript for Map Measure Plugin
* This software is copy righted by WatchTheMed 2013
* Writen by Dylan Gillespie, Etherton Technologies <http://ethertontech.com>
* Started on 2013-04-30
* This plugin is to add a ruler tool to the maps.
*************************************************************/
?>

<script type="text/javascript">	
	var path_info = '<?php echo $_SERVER['PATH_INFO']?>';
	var map_div = '';

	switch(path_info){
		case '/': 
		case '/main':
			map_div = 'map';
			break;
		case '/reports/submit':
			map_div = 'divMap';
			break;
		case '/reports':
			map_div = 'rb_map-view';
			break;
	}

	
	$(document).ready(function(){
		//create the ruler buttons
		$('#'+map_div).before(
				"<div id='control'>I am control</div><div id='rulerDiv' style='display:none'>\
					<input type='radio' value='line' name='ruler' id='lineDraw' onclick='toggleControl(this)'> Hey There</br>\
					<input type='radio' value='polygon' name='ruler' id='areaDraw' onclick='toggleControl(this)'> I draw areas\
				</div>\
				<div id = 'output'></div>\
					");

		//open the ruler buttons when clicked on
		$('#control').click(function(){
			$('#rulerDiv').toggle();
		});
	});


    var map, measureControls;
    function init(){
        map = new OpenLayers.Map(map_div);
        var wmsLayer = new OpenLayers.Layer.WMS( "OpenLayers WMS", 
                "http://vmap0.tiles.osgeo.org/wms/vmap0?", {layers: 'basic'}); 

            map.addLayers([wmsLayer]);
            map.addControl(new OpenLayers.Control.LayerSwitcher());
            map.addControl(new OpenLayers.Control.MousePosition());
            
        // style the sketch fancy
        var sketchSymbolizers = {
            "Point": {
                pointRadius: 4,
                graphicName: "square",
                fillColor: "white",
                fillOpacity: 1,
                strokeWidth: 1,
                strokeOpacity: 1,
                strokeColor: "#333333"
            },
            "Line": {
                strokeWidth: 3,
                strokeOpacity: 1,
                strokeColor: "#666666",
                strokeDashstyle: "dash"
            },
            "Polygon": {
                strokeWidth: 2,
                strokeOpacity: 1,
                strokeColor: "#666666",
                fillColor: "white",
                fillOpacity: 0.3
            }
        };
        var style = new OpenLayers.Style();
        console.log(map);
        console.log(wmsLayer);
        console.log(OpenLayers.Rule({symbolizer: sketchSymbolizers}));
        //console.log(new OpenLayers.Rule({'symbolizer': sketchSymbolizers}));
        style.addRules([
             new OpenLayers.Rule({symbolizer: sketchSymbolizers})
        ]);
        var styleMap = new OpenLayers.StyleMap({"default": style});
        
        // allow testing of specific renderers via "?renderer=Canvas", etc
        var renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
        renderer = (renderer) ? [renderer] : OpenLayers.Layer.Vector.prototype.renderers;

        measureControls = {
            line: new OpenLayers.Control.Measure(
                OpenLayers.Handler.Path, {
                    persist: true,
                    handlerOptions: {
                        layerOptions: {
                            renderers: renderer,
                            styleMap: styleMap
                        }
                    }
                }
            ),
            polygon: new OpenLayers.Control.Measure(
                OpenLayers.Handler.Polygon, {
                    persist: true,
                    handlerOptions: {
                        layerOptions: {
                            renderers: renderer,
                            styleMap: styleMap
                        }
                    }
                }
            )
        };
        
        var control;
        for(var key in measureControls) {
            control = measureControls[key];
            control.events.on({
                "measure": handleMeasurements,
                "measurepartial": handleMeasurements
            });
            map.addControl(control);
        }
        for(key in measureControls) {
            var control = measureControls[key];
            control.setImmediate(true);
        }
    }
    
    function handleMeasurements(event) {
        var geometry = event.geometry;
        var units = event.units;
        var order = event.order;
        var measure = event.measure;
        var element = document.getElementById('output');
        var out = "";
        if(order == 1) {
            out += "measure: " + measure.toFixed(3) + " " + units;
        } else {
            out += "measure: " + measure.toFixed(3) + " " + units + "<sup>2</" + "sup>";
        }
        element.innerHTML = out;
    }

    function toggleControl(element) {
        console.log('hey I changed control');
        for(key in measureControls) {
            var control = measureControls[key];
            if(element.value == key && element.checked) {
                control.activate();
            } else {
                control.deactivate();
            }
        }
    }
    
    function toggleImmediate(element) {
        for(key in measureControls) {
            var control = measureControls[key];
            control.setImmediate(element.checked);
        }
    }
	
	
</script>

<body onload='init()'>
