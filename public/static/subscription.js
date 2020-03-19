var isSubsripted = {SUBSCRIPTED};
var subscribeButton = document.getElementById('operbut');
function subscr() {
    const url = "subscribe.php";
    let deny = isSubsripted;
    var args = {'to': pageId, "deny": deny, "sessionId": sessionId};
    var success = function(resp) {
        if (deny === 0) {
            subscribeButton.classList.remove('btn-primary');
            subscribeButton.classList.add('btn-secondary');
            subscribeButton.innerText = "Subscribed";
            isSubsripted = 1;
        }
        else {
            subscribeButton.classList.remove('btn-secondary');
            subscribeButton.classList.add('btn-primary');
            subscribeButton.innerText = "Subscribe"
            isSubsripted = 0;
        }
    };
    var err = function(errno, resp) {
        /*
        ОБРАБОТАТЬ ОШИБКУ
         */
    };
    ajax('POST', url, args, success, err);
}