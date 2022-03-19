/*
Skillmenu (v 1.0)

by Mikay Kun
*/

function sm_setnormal()
{
	document.Skill.vdk.value=document.Skill.opp.value;
	document.Skill.vdef.value=document.Skill.odef.value;
	document.Skill.vatk.value=document.Skill.oatk.value;
	document.Skill.vhp.value=document.Skill.ohp.value;
	document.Skill.vff.value=document.Skill.off.value;

	window.document.getElementById("showrest").innerHTML=document.Skill.vdk.value;
	window.document.getElementById("shp").innerHTML=document.Skill.ohp.value;
	window.document.getElementById("sff").innerHTML=document.Skill.vff.value;
	window.document.getElementById("satk").innerHTML=document.Skill.vatk.value;
	window.document.getElementById("sdef").innerHTML=document.Skill.vdef.value;
}

function sm_hp(code)
{
	var rest=parseInt(document.Skill.vdk.value);
	var newhp=parseInt(document.Skill.vhp.value);
	var oldhp=parseInt(document.Skill.ohp.value);

	if (code==0)
	{
		newhp++;
		rest--;
	}

	else if (newhp>0 && oldhp<newhp)
	{
		newhp--;
		rest++;
	}

	if (rest>=0)
	{
		window.document.Skill.vdk.value=rest;
		window.document.getElementById("showrest").innerHTML=rest;
		window.document.Skill.vhp.value=newhp;
		window.document.getElementById("shp").innerHTML=newhp;
	}
}

function sm_ff(code)
{
	var rest=parseInt(document.Skill.vdk.value);
	var newff=parseInt(document.Skill.vff.value);
	var oldff=parseInt(document.Skill.off.value);

	if (code==0)
	{
		newff++;
		rest--;
	}

	else if (newff>0 && oldff<newff)
	{
		newff--;
		rest++;
	}

	if (rest>=0)
	{
		window.document.Skill.vdk.value=rest;
		window.document.getElementById("showrest").innerHTML=rest;
		window.document.Skill.vff.value=newff;
		window.document.getElementById("sff").innerHTML=newff;
	}
}

function sm_atk(code)
{
	var rest=parseInt(document.Skill.vdk.value);
	var newatk=parseInt(document.Skill.vatk.value);
	var oldatk=parseInt(document.Skill.oatk.value);

	if (code==0)
	{
		newatk++;
		rest--;
	}

	else if (newatk>0 && oldatk<newatk)
	{
		newatk--;
		rest++;
	}

	if (rest>=0)
	{
		window.document.Skill.vdk.value=rest;
		window.document.getElementById("showrest").innerHTML=rest;
		window.document.Skill.vatk.value=newatk;
		window.document.getElementById("satk").innerHTML=newatk;
	}
}

function sm_def(code)
{
	var rest=parseInt(document.Skill.vdk.value);
	var newdef=parseInt(document.Skill.vdef.value);
	var olddef=parseInt(document.Skill.odef.value);

	if (code==0)
	{
		newdef++;
		rest--;
	}

	else if (newdef>0 && olddef<newdef)
	{
		newdef--;
		rest++;
	}

	if (rest>=0)
	{
		window.document.Skill.vdk.value=rest;
		window.document.getElementById("showrest").innerHTML=rest;
		window.document.Skill.vdef.value=newdef;
		window.document.getElementById("sdef").innerHTML=newdef;
	}
}

function sm_checkbutton()
{
	var rest=document.Skill.vdk.value;

	var newdef=parseInt(document.Skill.vdef.value);
	var olddef=parseInt(document.Skill.odef.value);
	var newatk=parseInt(document.Skill.vatk.value);
	var oldatk=parseInt(document.Skill.oatk.value);
	var newhp=parseInt(document.Skill.vhp.value);
	var oldhp=parseInt(document.Skill.ohp.value);
	var newff=parseInt(document.Skill.vff.value);
	var oldff=parseInt(document.Skill.off.value);

	if (rest==0)
	{
		window.document.getElementById("smdefup").style.display='none';
		window.document.getElementById("smffup").style.display='none';
		window.document.getElementById("smatkup").style.display='none';
		window.document.getElementById("smhpup").style.display='none';
	}

	else
	{
		window.document.getElementById("smdefup").style.display='block';
		window.document.getElementById("smffup").style.display='block';
		window.document.getElementById("smatkup").style.display='block';
		window.document.getElementById("smhpup").style.display='block';
	}

	if (newdef==olddef)
	{
		window.document.getElementById("smdefdown").style.display='none';
		window.document.getElementById("sdef").style.color='#FFFFFF';
	}

	else
	{
		window.document.getElementById("smdefdown").style.display='block';
		window.document.getElementById("sdef").style.color='#00FF00';
	}

	if (newff==oldff)
	{
		window.document.getElementById("smffdown").style.display='none';
		window.document.getElementById("sff").style.color='#FFFFFF';
	}

	else
	{
		window.document.getElementById("smffdown").style.display='block';
		window.document.getElementById("sff").style.color='#00FF00';
	}

	if (newatk==oldatk)
	{
		window.document.getElementById("smatkdown").style.display='none';
		window.document.getElementById("satk").style.color='#FFFFFF';
	}

	else
	{
		window.document.getElementById("smatkdown").style.display='block';
		window.document.getElementById("satk").style.color='#00FF00';
	}

	if (newhp==oldhp)
	{
		window.document.getElementById("smhpdown").style.display='none';
		window.document.getElementById("shp").style.color='#FFFFFF';
	}

	else
	{
		window.document.getElementById("smhpdown").style.display='block';
		window.document.getElementById("shp").style.color='#00FF00';
	}
}

var timer = window.setInterval("sm_checkbutton()", 300);