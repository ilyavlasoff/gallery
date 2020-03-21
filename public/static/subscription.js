var isSubsripted = {SUBSCRIPTED};
var subscribeButton = document.getElementById('operbut');
window.addEventListener('load', checkSubscr);
function checkSubscr() {
    if (isSubsripted === 1) {
        subscribeButton.classList.remove('btn-primary');
        subscribeButton.classList.add('btn-secondary');
        subscribeButton.innerText = "Subscribed";
    }
    else {
        subscribeButton.classList.remove('btn-secondary');
        subscribeButton.classList.add('btn-primary');
        subscribeButton.innerText = "Subscribe";
    }
}
function subscr() {
    const url = "subscribe.php";
    let deny = isSubsripted;
    var args = {'to': pageId, "deny": deny, "sessionId": sessionId};
    var success = function(resp) {
        console.log(resp);
        if (deny === 0) {
            isSubsripted = 1;
        }
        else {
            isSubsripted = 0;
        }
        checkSubscr();
    };
    var err = function(errno, resp) {
        /*
        ОБРАБОТАТЬ ОШИБКУ
         */
    };
    ajax('POST', url, args, success, err);
}