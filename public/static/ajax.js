function getXmlHttp(){
    var xmlhttp;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            xmlhttp = false;
        }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

function ajax(_method = 'POST', _url, _args = null, _sucsessCallback = null, _errCallback = null) {
    const request = getXmlHttp();
    var send = '';
    if (_method.toUpperCase() === 'POST' && _args) {
        for(let i in _args) {
            send += i + "=" + _args[i] + "&";
        }
        send = send.substring(0, send.length-1);
    }
    else if (_method.toUpperCase() === 'GET') {
        send = null;
    }
    request.open(_method.toUpperCase(), _url, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(send);
    request.addEventListener("readystatechange", () => {
        if (request.readyState === 4) {
            if (request.status === 200 && _sucsessCallback) _sucsessCallback(request.responseText);
            else if (request.status !== 200 && _errCallback) _errCallback(request.status, request.responseText);
        }
    });

}