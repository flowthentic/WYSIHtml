var ajax = {};

ajax.submit = function(sender, callback) {
	var xmlreq = new XMLHttpRequest(), params;
	if (sender.form !== undefined)
	{
		params = new FormData(sender.form);
		params.append(sender.name, sender.value);
		sender = sender.form;
	}
	else params = new FormData(sender);
	var actAddress = sender.action;
	if (sender.method == 'get')
	{
		var firstRun = true;
		for (var key of params.keys())
		{
			if (firstRun)
			{
				actAddress += '?';
				firstRun = false;
			}
			else actAddress += '&';
		  actAddress += key + "=" + params.get(key);
		}
  }
	
	xmlreq.open(sender.method, actAddress, true);
	xmlreq.onreadystatechange = callback;
	if (sender.method == 'get')
		xmlreq.send();
	else xmlreq.send(params);
}

ajax.processHTML = function(target, insap)
{
  var ajax_fragmenthtml = target.cloneNode();
  ajax_fragmenthtml.removeAttribute('id');
  ajax_fragmenthtml.innerHTML = this.responseText;
  
  var newscripts = ajax_fragmenthtml.getElementsByTagName('script'), scriptn = 0;
  while (scriptn < newscripts.length) {
    try {
      eval(newscripts[scriptn].innerHTML);
      newscripts[scriptn].parentElement.removeChild(newscripts[scriptn]);
    } catch (err) {
      newscripts[scriptn].innerHTML += '/*'+err+'*/';
      scriptn++;
    }
  }
  
  var insertBeforeThis = target.children[0];
  if (insap == 'next') {
    insertBeforeThis = target.nextElementSibling; //append before next element
    insap = 'insert'; //if it is the last element, append will occur implicitly
    target = target.parentElement;  //make parent the target
  }
  else if (insap == 'previous')
  {
    insertBeforeThis = target;
    insap = 'insert';
    target = target.parentElement;
  }
  
  if (insap == 'insert' && target.childNodes.length > 0 && insertBeforeThis)
    while (ajax_fragmenthtml.children.length > 0)
      target.insertBefore(ajax_fragmenthtml.children[0], insertBeforeThis);
  else if (insap != 'append' && target.id != "" && target.id == ajax_fragmenthtml.children[0].id)
    target.parentElement.replaceChild(ajax_fragmenthtml.children[0], target);
  else
  {
    if (insap === undefined) target.innerHTML = '';
    while (ajax_fragmenthtml.children.length > 0)
     target.appendChild(ajax_fragmenthtml.children[0]);
  }
}

ajax.get = function (target, callback, paramback, notsync) {
  if (target instanceof NodeList) {
    for (i = 0; i < target.length; i++)
      if (target[i].href)
        target[i].addEventListener('click', (ev) => ajax.get(ev, callback, paramback), {capture:true});
    return;
  }
  else if (target instanceof Event) {
    target.preventDefault();
    target = target.currentTarget.href;
  }
  var request;
  if (window.XMLHttpRequest)
    request = new XMLHttpRequest();
  else if (window.ActiveXObject)
    request = new ActiveXObject("Microsoft.XMLHTTP");
  else throw new Error('Cannot create AJAX request');
  if (paramback == null) paramback = [];
  if (notsync === undefined) notsync = true;
  
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      callback.apply(request, paramback);
    }
  };
  request.open('get', target, notsync);
  request.send();
}

ajax.template = function(caller) {
  const tmpath = '/template/';
  if (caller === undefined) {
    var templaces = document.querySelectorAll('[template]');
    for (var tn = 0; tn < templaces.length; tn++)
      ajax.template(templaces[tn]);
  }
  else {
    var templatedata = caller.getAttribute('template').split(' ');
    for (var tempid = 0; tempid + 1 < templatedata.length; tempid += 2)
      ajax.get(tmpath+templatedata[tempid+1]+'.html', ajax.processHTML, new Array(caller, templatedata[tempid]));
  }
}
