<?php
function getSetupPage($this_page){
$page_html="<form action='setup.php' method='post' accept-charset='utf-8'>
	<table class='table_style2' style='clear: both;' cellspacing='0' width='100%'>
		<tbody>
			<caption>Please enter the above information...</caption>
		<tr>
			<th>You</th>
			<th>Your Email</th>
			<th>Your Password</th>
		</tr>
		<tr>
			<td>
				<label for='name'>Name</label><br>
				<input type='text' name='name' value='".$this_page->variable_stack['user_params']['name']."'><br>
			</td>
			<td>
				<label for='email'>Preferred E-Mail</label><br>
				<input type='text' name='email' value='".$this_page->variable_stack['user_params']['email']."'><br>
			</td>
			<td>
				<label for='password'>Password</label><br>
				<input type='password' name='password' value='".$this_page->variable_stack['user_params']['password']."'><br>
			</td>
		</tr>
	</table>
</form>";

return $page_html;
}
?>

