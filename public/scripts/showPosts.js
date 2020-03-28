var offset = 0;
var picturesContainer = document.getElementById("profile-content");
var loadModeButton = document.getElementById("loadmore");
function loadPics(_quantity = 100) {
    const url = "/getphotos";
    var args = {'pageId': pageId, 'quan': _quantity, "offset": offset };
    var success = function(data) {
        console.log(data);
        var resp = JSON.parse(data);
        console.log(resp);
        picturesContainer.innerHTML += resp.message;
        if (resp.loaded < _quantity) {
            loadModeButton.style.visibility = 'hidden';
        }
        offset += resp.loaded;
    };
    var err = function(errno, data) {
        console.log(data);
    };
    ajax('POST', url, args, success, err);
}