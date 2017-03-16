function ajaxObj(meth, url)
{
	var ajax = new XMLHttpRequest();
	ajax.open( meth, url, true );
	ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	return ajax;
}
function ajaxReturn(ajax){
	if(ajax.readyState == 4 && ajax.status == 200){
	    return true;
	}
}
