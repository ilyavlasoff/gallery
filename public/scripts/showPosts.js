var picturesContainer = document.getElementById("profile-content");
var loadModeButton = document.getElementById("loadmore");
function loadPics()
{
    var success = function (data) {
        var resp = JSON.parse(data);
        picturesContainer.innerHTML += resp.message;
        if (resp.loaded < quantity) {
            loadModeButton.style.visibility = 'hidden';
        }
        offset += resp.loaded;
    };
    var err = function (errno, data) {
        console.log(data);
    };
    ajax('POST', url, args, success, err);
}