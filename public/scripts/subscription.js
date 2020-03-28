var subscribeButton = document.getElementById('operbut');
document.addEventListener('onload', checkSubscription());

function subscr(check) {
    const url = "/subscribe";
    var args = {'to': pageId, "oper": check};
    var success = function(resp) {
        let isSubscribed = JSON.parse(resp).subscr;
        console.log(args);
        console.log(isSubscribed);
        if (isSubscribed === 1) {
            subscribeButton.classList.remove('btn-primary');
            subscribeButton.classList.add('btn-secondary');
            subscribeButton.innerText = "Subscribed";
            subscribeButton.removeEventListener('click', subscribe);
            subscribeButton.addEventListener('click', unsubscribe);
        }
        else {
            subscribeButton.classList.remove('btn-secondary');
            subscribeButton.classList.add('btn-primary');
            subscribeButton.innerText = "Subscribe";
            subscribeButton.removeEventListener('click', unsubscribe);
            subscribeButton.addEventListener('click', subscribe);
        }
    };
    var err = function(errno, resp) {
        console.log(resp);
    };
    ajax('POST', url, args, success, err);
}

function subscribe() {subscr('add')}
function unsubscribe() {subscr('cancel')}
function checkSubscription() { subscr('check'); }
