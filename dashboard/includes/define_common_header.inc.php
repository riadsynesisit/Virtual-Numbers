<?php
function common_header($this_page, $title, $show_menu=false, $meta_refresh_sec=0, $meta_refresh_page=""){
	
	$this_page->title = APP_TITLE_SHORT . " - " . $title;
	$this_page->site_name = APP_TITLE_LONG . " - " . INSTALL_LOCATION;
	//$this_page->css = array("template.css","admin_alerts.css","core.css","tablesorter.css");
	$this_page->scripts = array("jquery-1.10.1.min.js","pagination.js");
	$this_page->meta_refresh_sec = $meta_refresh_sec;
	$this_page->meta_refresh_page = $meta_refresh_page;
	
	$this_page->meta_tags = array( array("description","Sticky UK Number connect poeple together world wide. Free international UK number forwarding with instant account activation. Our servers provide free VOIP forwards to phone numbers accross the globe."),
		array("keywords","virtual UK number, free uk number, uk virtual phone number, free phone numbers uk, free uk numbers, free uk phone number, uk virtual number, uk phone numbers, uk telephone numbers, free telephone numbers uk, uk numbers, free uk voip number, uk virtual numbers, free uk phone numbers, free uk virtual number, uk number, uk phone numbers free, get free uk number, uk free number, uk free phone numbers"),
		array("rating","general"),
		array("Language","en"),
		array("robots","all"),
		array("robots","index,follow"),
		array("re-visit","7 days"),
		array("distribution","global"),
		array("X-UA-Compatible","IE=edge,chrome=1")
	);
	
	$this_page->template = getStandardPage($this_page);

	if($show_menu){
		$this_page->menu_items = application_menu();
		$this_page->menu_bar_vertical = false;
		//$this_page->menu_bar = $this_page->output_div_menu_bar(div_list=["nav","wide_960"])."\n";
	}else{
		$this_page->menu_bar = "";
	}//show_menu
	
	//Render the actual HTML
	echo $this_page->template;
}
?>