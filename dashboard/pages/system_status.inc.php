<?php
function getSystemStatusPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">System Status</h1>
               	</div>
            </div>
	';
	
	$page_html .= '<h4>This page will automatically refresh every minute.</h4>';
	
	$hdd_usage = $page_vars['hdd_usage'];
	$calls = $page_vars['calls'];
	$dids = $page_vars['dids'];
	$daily_total = $page_vars['daily_total'];
	$curr_host = strstr(PHP_OS, 'WIN') ? php_uname('n') : gethostname();
	
	$page_html .= '
	
	<table width="100%" cellspacing="0" class="table_style2">
	<tr>
		<td>
			<div class="table-responsive">
			<table class="table table-hover">
				<thead class="blue-bg">
				<tr>
					<th colspan=4>Service States</th>
				</tr>
				</thead>
				<tr>
					<td>
						<span class="right_text">asterisk('.trim($page_vars['asterisk_hostname']).')</span>
					</td>
					<td>
						<span class="status_'.$page_vars['asterisk_online'][0].'">
							'.$page_vars['asterisk_online'][1].'
						</span>
					</td>
					<td>
						<span class="right_text">mysql('.trim($page_vars['sql_hostname']).')</span>
					</td>
					<td>
						<span class="status_'.$page_vars['mysql_online'][0].'">
							'.$page_vars['mysql_online'][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">apache</span>
					</td>
					<td>
						<span class="status_'.$page_vars['apache_online'][0].'">
							'.$page_vars['apache_online'][1].'
						</span>
					</td>
					<td>
						<span class="right_text">smtp</span>
					</td>
					<td>
						<span class="status_'.$page_vars['smtp_online'][0].'">
							'.$page_vars['smtp_online'][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">ntpd</span>
					</td>
					<td>
						<span class="status_'.$page_vars['ntpd_online'][0].'">
							'.$page_vars['ntpd_online'][1].'
						</span>
					</td>
					<td>

					</td>
					<td>

					</td>
				</tr>

			</table>
			</div>
		</td>

		<td colspan = 2>
			<table class="table_style3" cellspacing="4"  width="100%" cellpadding="4">
				<tr>
					<th colspan=4>Resource Usage('.$curr_host.')</th>
				</tr>
				<tr>
					<td>
						<span class="right_text">cpu (apache)</span> 
					</td>
					<td>
						<span class="status_'.$page_vars['cpu_usage'][0].'">
							'.$page_vars['cpu_usage'][1].'
						</span>
					</td>
					<td>
						<span class="right_text">hard disk["Web /"]</span>
					</td>
					<td>
						<span class="status_'.$hdd_usage["web_root"][0].'">
							'.$hdd_usage["web_root"][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">cpu (asterisk)</span>
					</td>
					<td>
						<span class="status_'.$page_vars['cpu_ast_usage'][0].'">
							'.$page_vars['cpu_ast_usage'][1].'
						</span>
					</td>
					<td>
						<span class="right_text">hard disk["Web Content"]</span>
					</td>
					<td>
						<span class="status_'.$hdd_usage["web_content"][0].'">
							'.$hdd_usage["web_content"][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">cpu (MySQL)</span>
					</td>
					<td>
						<span class="status_'.$page_vars['cpu_sql_usage'][0].'">
							'.$page_vars['cpu_sql_usage'][1].'
						</span>
					</td>
					<td>
						<span class="right_text">hard disk["asterisk /"]</span>
					</td>
					<td>
						<span class="status_'.$hdd_usage["asterisk_root"][0].'">
							'.$hdd_usage["asterisk_root"][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">bandwidth</span>
					</td>
					<td>
						<span class="status_'.$page_vars['bandwidth_usage'][0].'">
							'.$page_vars['bandwidth_usage'][1].'
						</span>
					</td>
					<td>
						<span class="right_text">hard disk["Asterisk content"]</span>
					</td>
					<td>
						<span class="status_'.$hdd_usage["asterisk_content"][0].'">
							'.$hdd_usage["asterisk_content"][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
					<td>
						&nbsp;
					</td>
					<td>
						<span class="right_text">hard disk["DB /"]</span>
					</td>
					<td>
						<span class="status_'.$hdd_usage["sql_root"][0].'">
							'.$hdd_usage["sql_root"][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
					<td>
						&nbsp;
					</td>
					<td>
						<span class="right_text">hard disk["DB Data Directory"]</span>
					</td>
					<td>
						<span class="status_'.$hdd_usage["sql_data"][0].'">
							'.$hdd_usage["sql_data"][1].'
						</span>
					</td>
				</tr>
			</table>			
		</td>
		
	</tr>
	<tr>
		<td>
			<table class="table_style3" cellspacing="4" width="100%" cellpadding="4">
				<tr>
					<th colspan=2>
						Active Calls('.trim($page_vars['asterisk_hostname']).')
					</th>
				</tr>
				<tr>
					<td>
						<span class="right_text">incoming(070)</span>
					</td>
					<td>
						<span class="status_'.$calls['incoming'][0].'">
							'.$calls['incoming'][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">outgoing(070)</span>
					</td>
					<td>
						<span class="status_'.$calls['outgoing'][0].'">
							'.$calls['outgoing'][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">concurrent(070)</span>
					</td>
					<td>
						<span class="status_'.$calls['concurrent'][0].'">
							'.$calls['concurrent'][1].'
						</span>
					</td>
				</tr>
				<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
				<tr>
					<td>
						<span class="right_text">incoming(087)</span>
					</td>
					<td>
						<span class="status_'.$calls['incoming_087'][0].'">
							'.$calls['incoming_087'][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">outgoing(087)</span>
					</td>
					<td>
						<span class="status_'.$calls['outgoing_087'][0].'">
							'.$calls['outgoing_087'][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">concurrent(087)</span>
					</td>
					<td>
						<span class="status_'.$calls['concurrent_087'][0].'">
							'.$calls['concurrent_087'][1].'
						</span>
					</td>
				</tr>
			</table>
		</td>

		<td>
			<table class="table_style3" cellspacing="4" width="100%" cellpadding="4">
				<tr><th colspan=2>DIDs</th></tr>
				<tr>
					<td>
						<span class="right_text">in service(070)</span>
					</td>
					<td>
						<span class="status_'.$dids['in_service'][0].'">
							'.$dids['in_service'][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">in service(087)</span>
					</td>
					<td>
						<span class="status_'.$dids['in_service_087'][0].'">
							'.$dids['in_service_087'][1].'
						</span>
					</td>
				</tr>
				<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
				<tr>
					<td>
						<span class="right_text">remaining(070)</span>
					</td>
					<td>
						<span class="status_'.$dids['remaining'][0].'">
							'.$dids['remaining'][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">reorder threshold (070)</span>
					</td>
					<td>
						<form action="admin_system_status.php" method="post" accept-charset="utf-8">
						<input type="text" name="reorder_threshold" value="'.$dids["reorder_threshold"].'" class="reorder_threshold" size="2">
						<input type="submit" value="save">
						</form>
					</td>
				</tr>
				<tr>
					<td><span class="right_text">070 Series</td>
					<td>
						<form action="admin_system_status.php" method="post" accept-charset="utf-8">
						<input type="text" name="did_070_start" value="'.$dids["did_070_start"].'" class="reorder_threshold" size="7">
						<input type="submit" value="save">
						</form>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">remaining(087)</span>
					</td>
					<td>
						<span class="status_'.$dids['remaining_087'][0].'">
							'.$dids['remaining_087'][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">reorder threshold (087)</span>
					</td>
					<td>
						<form action="admin_system_status.php" method="post" accept-charset="utf-8">
						<input type="text" name="087reorder_threshold" value="'.$dids["087reorder_threshold"].'" class="reorder_threshold" size="2">
						<input type="submit" value="save">
						</form>
					</td>
				</tr>
			</table>
		</td>
	
		<td>
			<table class="table_style3" cellspacing="4" width="100%" cellpadding="4">
				<tr><th colspan=2>Daily Totals</th></tr>
				<tr>
					<td>
						<span class="right_text">calls(070)</span>
					</td>
					<td>
						<span class="status_'.$daily_total["calls"][0].'">
							'.$daily_total["calls"][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">calls(087)</span>
					</td>
					<td>
						<span class="status_'.$daily_total["calls_087"][0].'">
							'.$daily_total["calls_087"][1].'
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="right_text">signups</span>
					</td>
					<td>
						<span class="status_'.$daily_total["signups"][0].'">
							'.$daily_total["signups"][1].'
						</span>
					</td>
				</tr>
			</table>
		</td>

	</tr>
</table>
	
	';
	$page_html.='</div>';
	$page_html.='</div>';
	$page_html .= '</div>';
	
	return $page_html;
	
}
?>