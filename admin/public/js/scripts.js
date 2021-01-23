// JavaScript Document
function confirmation() {
		var answer = confirm("Та үнэхээр устгах гэж байна уу?")
		if (answer){			
			return true;
		}
		else{
			return false;
		}
}

function textCounter(field,cntfield,maxlimit) {
	if (field.value.length > maxlimit) // if too long...trim it!
	field.value = field.value.substring(0, maxlimit);
	// otherwise, update 'characters left' counter
	else
	cntfield.value = maxlimit - field.value.length;
}

function validate_form(thisform)
{
	with (thisform)
 	{
  		if (date.value=="-1")
  		{
			alert("Өдөрөө сонго.");
			//document.getElementById("error"). = "aldaaaa";
			date.focus();return false;
		}
	}
}

function aaa()
{
	alert("test");
}

function checkNumber(event,F)
{
	var txt = F.value;
	var chr = '';
		switch(event.keyCode) {
			case 96: case 48: // 0 and numpad 0
				chr = '0';
				break;
			case 97: case 49: // 1 and numpad 1
				chr = '1';
				break;
			case 98: case 50: // 2 and numpad 2
				chr = '2';
				break;
			case 99: case 51: // 3 and numpad 3
				chr = '3';
				break;
			case 100: case 52: // 4 and numpad 4
				chr = '4';
				break;
			case 101: case 53: // 5 and numpad 5
				chr = '5';
				break;
			case 102: case 54: // 6 and numpad 6
				chr = '6';
				break;
			case 103: case 55: // 7 and numpad 7
				chr = '7';
				break;
			case 104: case 56: // 8 and numpad 8
				chr = '8';
				break;
			case 105: case 57: // 9 and numpad 9
				chr = '9';
				break;
			case 110: case 190: // 9 and numpad 9
				chr = '.';
				break;
			default:
				chr = ''; // key pressed as a lowercase string
				break;
		}
	F.value = txt.substring(0,F.value.length-1) + chr;
}