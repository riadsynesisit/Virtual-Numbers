function form_check(formobj, fieldRequired, fieldDescription){
	// dialog message
	var alertMsg = "Please complete the following required fields:\n";

	var l_Msg = alertMsg.length;

	for (var i = 0; i < fieldRequired.length; i++){
		var obj = formobj.elements[fieldRequired[i]];
		
		if (obj){
			var classes = obj.className.replace(/ form_validation_error/g,'');
			obj.className = classes;
			
			clear_request_forms();
			
			switch(obj.type){
				case "select-one":
					if (obj.selectedIndex == 0 || obj.options[obj.selectedIndex].text == ""){
						alertMsg += " - " + fieldDescription[i] + "\n";
						obj.className = classes + ' form_validation_error';
					} 
				break;
				case "select-multiple":
				case "select":
					if (obj.selectedIndex == 0){
						alertMsg += " - " + fieldDescription[i] + "\n";
						obj.className = classes + ' form_validation_error';
					}
				break;
				case "file":
				case "text":
				case "password":
				case "textarea":
					if (fieldRequired[i] == 'email' && !obj.value.match(/^[a-zA-Z0-9\._\-]*@[a-zA-Z0-9\.\-]*\.[a-zA-Z]{2,6}(\.[a-zA-Z]{2,4})?$/)) {
						alertMsg += " - " + fieldDescription[i] + "\n";
						obj.className = classes + ' form_validation_error';
					} else if (obj.value == "" || obj.value == null){
						alertMsg += " - " + fieldDescription[i] + "\n";
						obj.className = classes + ' form_validation_error';
					}
				break;
				case "checkbox":
					if (obj.checked != true){
						alert(fieldDescription[i]);
						obj.className = classes + ' form_validation_error';
					}
				break;
				default:
			}

			if (obj.type == undefined){
				var blnchecked = false;

				for (var j = 0; j < obj.length; j++){
					if (obj[j].checked){
						blnchecked = true;
					}
				}

				if (!blnchecked){
					alertMsg += " - " + fieldDescription[i] + "\n";
					obj.className = classes + ' form_validation_error';
				} 
			}
		}
	}

	if (alertMsg.length == l_Msg){
		return true;
	} else {
		redo_request_forms();
		alert(alertMsg);
		return false;
	}
}