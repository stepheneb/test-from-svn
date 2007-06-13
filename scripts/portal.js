// This is the main utility script file for the portal

/* This code from Yahoo! gives us the resize functionality */

YAHOO.example.DDResize = function(panelElId, handleElId, sGroup, config) {
    if (panelElId) {
        this.init(panelElId, sGroup, config);
        this.handleElId = handleElId;
        this.setHandleElId(handleElId);
        this.logger = this.logger || YAHOO;
    }
};

YAHOO.extend(YAHOO.example.DDResize, YAHOO.util.DragDrop);

YAHOO.example.DDResize.prototype.onMouseDown = function(e) {
    var panel = this.getEl();
    this.startWidth = panel.offsetWidth;
    this.startHeight = panel.offsetHeight;

    this.startPos = [YAHOO.util.Event.getPageX(e),
                     YAHOO.util.Event.getPageY(e)];
};

YAHOO.example.DDResize.prototype.onDrag = function(e) {
    var newPos = [YAHOO.util.Event.getPageX(e),
                  YAHOO.util.Event.getPageY(e)];

    var offsetX = newPos[0] - this.startPos[0];
    var offsetY = newPos[1] - this.startPos[1];

    var newWidth = Math.max(this.startWidth + offsetX, 10);
    var newHeight = Math.max(this.startHeight + offsetY, 10);

    var panel = this.getEl();
    panel.style.width = newWidth + "px";
    panel.style.height = newHeight + "px";
};

function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			if (oldonload) {
				oldonload();
			}
			func();
		}
	}
}

function create_new_panel() {

	var cnt, p, e, new_panel, panel_content;
	
	cnt = panels.length + 1;
	
	// set up the new panel object
	
	p = new Object();
	
	p.pid = 'panel' + cnt;
	
	p.hid = 'handle' + cnt;
	
	p.iid = 'input' + cnt;
	
	p.dd = new YAHOO.example.DDResize(p.pid, p.hid, "panelresize");
	
	p.dd2 = new YAHOO.util.DD(p.pid, "paneldrag");
	
	p.dd2.addInvalidHandleId(p.hid); 	// this makes the handle usable for drag and drop

	panels.push(p);
	
	// add this panel to the html content
	
	panel_content = '<input type="button" value="X" onclick="delete_panel(this);"> <input type="button" value="i" onclick="alert(\'id: ' + p.iid + ' - You will be able to change data by clicking on this link.\');"> <div id="' + p.hid + '" class="panelHandle"><' + '/div>';
	
	new_panel = document.createElement('div');
	new_panel.id = p.pid;
	new_panel.className = 'panelArea';
	new_panel.innerHTML = panel_content;
	
	document.getElementById('studysiteimage').appendChild(new_panel);

}

function array_remove(a, item) {

	new_array = new Array();
	for (var i =  0; i < a.length; i++) {
		if (a[i] != item) {
			new_array[(new_array.length)] = a[i];
		}
	}

	a = new Array();
	for (h = 0; h < new_array.length; h++) {
		a[h] = new_array[h];
	}
		
	return a;

}

function delete_panel(p) {

	if (confirm('Are you sure you want to delete this panel?','Yes','No')) {
	
		// remove it from the array of panels
		//panels = array_remove(panels, p.parentNode);
	
		// delete the element
		delete_parent(p);
		
	}

}

function write_to_element(element_id, content) {

	var this_element = document.getElementById(element_id);
	this_element.innerHTML = content;

}

function array_search(needle, haystack) {

	if (haystack.length == 0) { return false; }
	
	for (var i = 0; i < haystack.length; i++) {
		if (needle == haystack[i]) { return true; }
	}
	
	return false;

}

function get_field_value_by_name(element_name) {

	// warning... if result is an integer, you'll need to cast it as such
	var these_values = new Array();
	var element_list = document.getElementsByTagName('input');
	for (var i = 0; i < element_list.length; i++) {
		if (element_list[i].name == element_name && element_list[i].checked==1) {
			these_values[these_values.length] = element_list[i].value;
		}
	}
	return these_values.join(',');

}

function match_checkboxes(master_id, element_name) {

	// master_name is the "id" of the field you're trying to match
	//    by giving it an id, we don't need to look through the whole object heirarchy
	// element name is the "name" of elements you're trying to change
	
	var master_setting = document.getElementById(master_id).checked;

	var element_list = document.getElementsByTagName('input');
	for (var i = 0; i < element_list.length; i++) {
		if (element_list[i].name == element_name) {
			element_list[i].checked = master_setting;
		}
	}

}

function checkbox_select_all(element_name) {

	var element_list = document.getElementsByTagName('input');
	for (var i = 0; i < element_list.length; i++) {
		if (element_list[i].name == element_name) {
			element_list[i].checked = 1;
		}
	}

}

function checkbox_select_none(element_name) {

	var element_list = document.getElementsByTagName('input');
	for (var i = 0; i < element_list.length; i++) {
		if (element_list[i].name == element_name) {
			element_list[i].checked = 0;
		}
	}

}

function checkbox_select_opposite(element_name) {

	var element_list = document.getElementsByTagName('input');
	for (var i = 0; i < element_list.length; i++) {
		if (element_list[i].name == element_name) {
			element_list[i].checked = !element_list[i].checked;
		}
	}

}

function get_select_box_value(element_id) {

	var e = document.getElementById(element_id);
	
	return e.options[e.selectedIndex].value;
	
}

function set_select_box_value(element_id, element_value) {

	var e = document.getElementById(element_id);
	
	for (var i = 0; i < e.options.length; i++) {
		if (e.options[i].value == element_value) {
			e.selectedIndex=i;
		}
	}

}

function select_box_link(element_id, link_stub) {

	// this function gets the value of a select box, adds it to the 
	// link stub and redirects the users to that page.
	// it is used for selectbox/icon combo tools
	
	var param = get_select_box_value(element_id);
	
	var url = link_stub + param + '/';
	
	window.location.href=url;

}


function hide_element(element_id) {

	var e = document.getElementById(element_id);
	e.style.display = 'none';	

}

function show_inline_element(element_id) {

	var e = document.getElementById(element_id);
	e.style.display = 'inline';	

}

function show_block_element(element_id) {

	var e = document.getElementById(element_id);
	e.style.display = 'block';	

}

function toggle_block_element(element_id) {

	var e = document.getElementById(element_id);

	if (e.style.display == 'block') {
		e.style.display = 'none';
	} else {
		e.style.display = 'block';
	}

}



// functions for removing elements

function deleteParent(element) {

	var parent = element.parentNode;
	var grandparent = parent.parentNode;
	grandparent.removeChild(parent);

}

function deleteGrandParent(element) {

	var parent = element.parentNode;
	var grandparent = parent.parentNode;
	var greatgrandparent = grandparent.parentNode;
	greatgrandparent.removeChild(grandparent);

}

function deleteGreatGrandParent(element) {

	var parent = element.parentNode;
	var grandparent = parent.parentNode;
	var greatgrandparent = grandparent.parentNode;
	var greatgreatgrandparent = greatgrandparent.parentNode;
	greatgreatgrandparent.removeChild(greatgrandparent);

}

function deleteGreatGreatGrandParent(element) {

	var parent = element.parentNode;
	var grandparent = parent.parentNode;
	var greatgrandparent = grandparent.parentNode;
	var greatgreatgrandparent = greatgrandparent.parentNode;
	var greatgreatgreatgrandparent = greatgreatgrandparent.parentNode;
	greatgreatgreatgrandparent.removeChild(greatgreatgrandparent);

}


// Functions for the activity grid

/*function toggle_section(section_id) {

	var section = document.getElementById(section_id);
	
	console.log(section.style.display);
	
	if (section.style.display == "none" || section.style.display == "" || section.style.display == null) {
		section.style.display = "block";
		//link_object.innerHTML = "hide";
	} else {
		section.style.display = "none";
		//link_object.innerHTML = "expand";
	}

}*/

function hide_all_sections() {

	divs = document.getElementsByTagName("DIV");
	
	for (var i = 0; i < divs.length; i++) {
	
		if (divs[i].className.match("unit-activities")) {
		
			divs[i].style.display = "none";
		
		}
	
	}
	
	lis = document.getElementsByTagName("LI");
	
	for (var i = 0; i < lis.length; i++) {
	
		if (lis[i].className.match("unit-navigation")) {
		
			lis[i].style.borderRightColor = '#666666';
		
		}
	
	}

}

function show_section(section_id, link_object) {

	hide_all_sections();

	var section = document.getElementById(section_id);
	
	section.style.display = "block";
	
	var bc = '';
	
	if (link_object.className.match("level1")) {
		bc = '#A3BDA2';
	} else if (link_object.className.match("level2")) {
		bc = '#C6958B';
	} else if (link_object.className.match("level3")) {
		bc = '#BA9C61';
	} else if (link_object.className.match("level999")) {
		bc = '#D6C754';
	}
	
	
	
	link_object.style.borderRightColor = bc;

}


function select_activity_checkboxes(section_id, checkbox_control) {

	var section = document.getElementById(section_id);
	
	var inputs = section.getElementsByTagName("INPUT");
	
	for (var i = 0; i < inputs.length; i++) {
	
		if (inputs[i].type == 'checkbox') {
		
			if (inputs[i].checked != checkbox_control.checked) {
			
				inputs[i].checked = checkbox_control.checked;
				
				updateTotalActivities(inputs[i]);
			
			}
		
		}
	
	}


}

