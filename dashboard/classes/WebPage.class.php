<?php

class WebPage{
	
	public $is_border_page = false;
	public $variable_stack = array();
	public $content_area;
	public $site_name;
	public $title;
	public $params;
	public $css;
	public $scripts;
	public $meta_tags;
	public $meta_refresh;
	public $meta_refresh_sec;
	public $meta_refresh_page;
	public $template;
	public $banner;
	public $header;
	public $footer;
	public $menu_bar;
	public $menu_bar_vertical;
	public $menu_items;
	public $date_time;
	
	function __construct(){
		
		$script_name = explode("/",$_SERVER['SCRIPT_NAME']);
		$script_name = $script_name[count($script_name) -1];
		if($script_name=="login.php" || $script_name=="logout.php"||$script_name=="reset.php"||$script_name=="setup.php"){
			$this->is_border_page = true;			
		}
		$this->meta_refresh = array(-1,"");
	}
	
	/*function refresh_meta_tag(){
		
		$html = "";
		
		if($this->meta_refresh_sec!="" && $this->meta_refresh_sec > 0 && $this->meta_refresh_page!=""){
			$html = '<meta http-equiv="refresh" content="'.$this->meta_refresh_sec.';url='.$this->meta_refresh_page.'" />\n";
		}
		
		return $html;
		
	}*/
	
}
?>