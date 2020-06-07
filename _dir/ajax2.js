function langObj_aj()
{
	var langObj = {
	
// *** Multi-language configuration array start ***
zh: {
js_1: '创建XMLHttp请求对象错误!',
js_2: '程序执行错误:',
js_3: '请将错误信息发送给技术支持'
},
en: {
js_1: 'Creating XMLHttp Request object error!',
js_2: 'Program execution error:',
js_3: 'Please send the wrong message to Technical Support'
}
// *** Multi-language configuration array end ***
	};
	return langObj;
}
function ajaxCall(callback, url, target, error)
{
	var handle;
	var msxmlhttp = new Array('Msxml2.XMLHTTP.5.0', 'Msxml2.XMLHTTP.4.0', 'Msxml2.XMLHTTP.3.0',
		'Msxml2.XMLHTTP', 'Microsoft.XMLHTTP');
	for (var i = 0; i < msxmlhttp.length; i++)
	{
		try
		{
			handle = new ActiveXObject(msxmlhttp[i]);
		}
		catch (e)
		{
			handle = null;
		}
	}

	if(!handle && typeof XMLHttpRequest != "undefined")
		handle = new XMLHttpRequest();
	if (!handle)
	{
		alert(LG(langObj_aj(),"js_1"));
		return false;
	}

	handle.open("GET", url, true);
	
	handle.onreadystatechange = function()
	{
		if (handle.readyState != 4)
			return;
		if (handle.status != 200)
		{
			if(error)
				alert(error);
			//else
				//alert(handle.responseText);
			return;
		}

		var data = handle.responseText.replace(/^\s*|\s*$/g,"");
		try{
			if( !target ) {
    			var result = eval(data);
    			callback( result );
			}
			else {
				document.getElementById(target).innerHTML=data;
			}
		}
		catch(e)
		{
			alert(LG(langObj_aj(),"js_2")+"【" + e.message + "】"+LG(langObj_aj(),"js_3")+"\n" + data);
			return;
		}
	};
	handle.send();
	delete handle;
	return true;
}


