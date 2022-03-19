// by talion
function com_prev () {
	
	var str = document.getElementById('comin').value;	
	var i = 0;
	var out = '';
	
	if(str.substr(0,4) == '/msg') {
		out = "<span class='c"+String('7').charCodeAt(0)+"'><b>";
		str = str.substring(4,str.length);
		str += ' </b>';
	}
	else {
		
		ecol = ecol!='' ? ecol : '&';
		
		var wh = "<span class='c"+ecol.charCodeAt(0)+"'>";
		
		if(str.substr(0,3) == '/me') {
			out = wh;
			str = str.substring(3,str.length);
		}
		else if(str.substr(0,2) == '::') {
			out = wh;
			str = str.substring(2,str.length);
		}
		else if(str.substr(0,1) == ':') {		
			out = wh;
			str = str.substring(1,str.length);			
		}
		else {
			var sh = "<span class='c"+tcol.charCodeAt(0)+"'>";
			out = "<span class='c"+String('3').charCodeAt(0)+"'> "+verb+': '+sh+'"';
			str += '"</span>';
		}
		str = str;
		out = name+out;		
		
	}
			
	str = prev(str,mx);
	
	out = out + str;
	
	out = out.replace(/%x(\d)/g,'<i>shortcut$1</i>');
	
	document.getElementById('comprev').innerHTML = out; 	

}

function prev (inp,mx) {
    
    var r,lst;
    var out = '';
    var sp = '';
    var i,c = 0;
    
    inp = inp.replace(/[`][`]/g,'');
		
	lst = inp.split('`');
	
	for(i=0; i<lst.length; i++) {
		
		r = lst[i].charAt(0);
		
		if(r == '0' && sp != '') {
			out = out + '</span>'+lst[i].slice(1);	
			sp = '';
		}
		else {
			if(r != '' && reg.indexOf(r) > -1 && c < mx && i > 0) {
				out = out + sp + '<span class="c'+r.charCodeAt(0)+'">'+lst[i].slice(1);	
				sp = '</span>';
				c++;
			}
			else {
				out = out + lst[i];
			}
		}
						
	}
	
	out += sp;
					
	return(out);
    
}

function input_prev (field) {
	
	document.getElementById(field.name + '_prev').innerHTML = prev(field.value,20); 	

}
