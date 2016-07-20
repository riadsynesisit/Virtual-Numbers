<?php

function show_admin_menu(){
	
	$admin_menu = "";
	
	if(isset($_SESSION['admin_flag']) && $_SESSION['admin_flag']==1){
	
		/*$admin_menu .= '
		
			<table class="admin_table" border="0" cellspacing="0">
			  	<tbody>
				  <tr>
						<th class="title_th"> System Manager</th>
					</tr>
					<tr>
					  <td><a href="admin_system_status.php">Current System Status</a></td>
					</tr>
					<tr>
					  <td><a href="admin_system_notices.php">Add or Review Current System Notices</a></td>
					  </tr>
					<tr>
					  <td><a href="admin_did_numbers.php">Edit or Review Current Live Virtual Numbers</a></td>
				  </tr>
					<tr>
					  <td><a href="admin_dest_costs_mgr.php">Edit or Review Call Destination Costs</a></td>
				  </tr>
				  <tr>
					  <td><a href="search.php">Member Search</a></td>
				  </tr>
				</tbody>
			</table>
		
		';*/
		
		/*$admin_menu .='<div class="green-tab">
					<h3>System Manager</h3>
						
						<ul>
							<li';
							if(isset($this_page->variable_stack['menu_item']) && $this_page->variable_stack['menu_item']=='index') { $admin_menu.=' class="active" ' ;}
							$admin_menu.='><a href="admin_system_status.php"><span>Current System Status</span></a></li>
							<li';
							if(isset($this_page->variable_stack['menu_item']) && $this_page->variable_stack['menu_item']=='index') { $admin_menu.=' class="active" ' ;}
							$admin_menu.='><a href="admin_system_notices.php"><span>Current System Notices</span></a></li>
							<li';
							if(isset($this_page->variable_stack['menu_item']) && $this_page->variable_stack['menu_item']=='index') { $admin_menu.=' class="active" ' ;}
							$admin_menu.='><a href="admin_did_numbers.php"><span>Live Virtual Numbers</span></a></li>
							<li';
							if(isset($this_page->variable_stack['menu_item']) && $this_page->variable_stack['menu_item']=='index') { $admin_menu.=' class="active" ' ;}
							$admin_menu.='><a href="admin_dest_costs_mgr.php"><span>Call Destination Costs</span></a></li>
							<li';
							if(isset($this_page->variable_stack['menu_item']) && $this_page->variable_stack['menu_item']=='member_search') { $admin_menu.=' class="active" ' ;}
							$admin_menu.='><a href="search.php"><span>Member Search</span></a></li>
						</ul>
					</div> ';*/
	}
	
	return $admin_menu;
	
}

?>