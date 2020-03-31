function subscribeToSinglePage()
{
    var subscribeButton = document.getElementById('operbut');
    subscr(subscribeButton, pageId, 'check');
}

function subscribeToMultiplePages()
{
    var subButtons = document.getElementsByName('sub');
    subButtons.forEach(function(item) {
        subscr(item, item.getAttribute('id'), 'check');
    })
}


function subscr(btn, pageId, oper)
{
    const url = "/subscribe";
    var args = {'to': pageId, "oper": oper};
    var success = function (resp) {
        let isSubscribed = JSON.parse(resp).subscr;
        if (isSubscribed === 1) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-secondary');
            btn.innerText = "Subscribed";
            btn.removeEventListener('click', function() { subscr(btn, pageId, 'add'); });
            btn.addEventListener('click', function() { subscr(btn, pageId, 'cancel'); });
        } else {
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-primary');
            btn.innerText = "Subscribe";
            btn.removeEventListener('click', function() { subscr(btn, pageId, 'cancel'); });
            btn.addEventListener('click', function() { subscr(btn, pageId, 'add'); });
        }
    };
    var err = function (errno, resp) {
        console.log(resp);
    };
    ajax('POST', url, args, success, err);
}
