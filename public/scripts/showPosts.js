var picturesContainer = document.getElementById("profile-content");
var loadModeButton = document.getElementById("loadmore");
function loadPics() {
    var success = function(data) {
        console.log(data);
        var resp = JSON.parse(data);
        console.log(resp);
        picturesContainer.innerHTML += resp.message;
        if (resp.loaded < quantity) {
            loadModeButton.style.visibility = 'hidden';
        }
        offset += resp.loaded;
    };
    var err = function(errno, data) {
        console.log(data);
    };
    console.log(args);
    ajax('POST', url, args, success, err);
}