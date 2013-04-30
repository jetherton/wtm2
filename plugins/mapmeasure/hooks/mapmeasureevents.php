<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Viddler - Load All Events
 */

class mapmeasureevents {


	public function __construct()
	{
		Event::add('system.pre_controller', array($this, 'add'));
	
		
	}

	public function add()
	{
		Event::add('ushahidi_action.header_scripts', array($this, 'render_javascript'));
	}
	
	public function render_javascript(){
		$view = new View('mapmeasure/mapmeasure_js');
		echo $view;
	}
	
}
new mapmeasureevents;