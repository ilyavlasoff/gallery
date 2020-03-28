var underlay = document.getElementById('underlay');
var photoBlock = document.getElementById('openImageViewver');
var imageContainer = document.getElementById('picture');
document.getElementById('profile-content').onclick = function(event) {
    let target = event.target;
    const url = '/post';
    if (target.className === 'ph') {
        let id = target.id;
        var args = {'id': id };
        var success = function(data) {
            console.log(data);
            var resp = JSON.parse(data);
            underlay.classList.add('underlay-show');
            photoBlock.classList.add('block-show');
            var pic = document.createElement('img');
            pic.setAttribute('src', resp.path);
            pic.classList.add('contain-fit');
            imageContainer.append(pic);
            document.getElementById('profilepic').setAttribute('src', resp.profilePicPath);
            document.getElementById('ownerLink').setAttribute('href', resp.ownerLink);
            document.getElementById('ownerName').innerHTML = resp.ownerName;
            document.getElementById('date').innerHTML = resp.date;
            document.getElementById('marksCount').innerHTML = 'Total marks: ' + resp.marksCount;
            document.getElementById('marksMean').innerHTML = 'Average mark: ' + resp.marksAvg;
            //date
            //comments
            //likes
            //marksCount
            //marksMean
        };
        var err = function(errno, data) {
            console.log(data);
        };
        ajax('POST', url, args, success, err);
    }

};

underlay.addEventListener('click', function () {
    console.log('hello');
    var image = imageContainer.lastElementChild;
    imageContainer.removeChild(image);
    underlay.classList.remove('underlay-show');
    photoBlock.classList.remove('block-show');
});

document.onresize = function(){
    alert('Размеры div #Test изменены.');
};

