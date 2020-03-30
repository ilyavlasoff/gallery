var likeValueOnPage;
var underlay = document.getElementById('underlay');
var photoBlock = document.getElementById('openImageViewver');
var imageContainer = document.getElementById('picture');
var id;
document.getElementById('profile-content').onclick = function (event)
{
    let target = event.target;
    const url = '/post';
    if (target.className === 'ph') {
        id = target.id;
        var args = {'id': id };
        var success = function (data) {
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
            if (resp.description !== '') {
                var comment = document.createElement('p');
                comment.innerHTML = '<b>' + resp.ownerName + '</b> ' + resp.description;
                document.getElementById('comments').appendChild(comment);
            }
            likeValueOnPage = parseInt(resp.yourMark);
            displayLike(likeValueOnPage);
        };
        var err = function (errno, data)
        {
            console.log(data);
        };
        ajax('POST', url, args, success, err);
    }

};

underlay.addEventListener('click', function () {
    var image = imageContainer.lastElementChild;
    var comment = document.getElementById('comments').firstChild;
    imageContainer.removeChild(image);
    if(comment)
        document.getElementById('comments').removeChild(comment);
    underlay.classList.remove('underlay-show');
    photoBlock.classList.remove('block-show');
});

var hearts = document.getElementById('likes').children;

for(let i =0; i!== hearts.length; i++) {
    hearts[i].addEventListener('mouseover', function() {
        displayLike(parseInt(hearts[i].getAttribute('id')) + 1);
    });
    hearts[i].addEventListener('mouseleave', function() {
        displayLike(likeValueOnPage);
    });
    hearts[i].addEventListener('click', function() {
        setLike(parseInt(hearts[i].getAttribute('id')) + 1);
    });
}

function displayLike(val) {
    var hearts = document.getElementById('likes').children;
    console.log(typeof (val));
    for(let i=0; i!== val && i<hearts.length; i++) {
        hearts[i].setAttribute('style', 'color: red;')
    }
    for(let i=val; i< hearts.length; i++) {
        hearts[i].setAttribute('style', 'color: gray;')
    }
}

function setLike(value) {
    let url = '/mark';
    let args = {'id': id, 'value': value};
    var success = function(data) {
        console.log(data);
        var resp = JSON.parse(data);
        var value = parseInt(resp.message);
        likeValueOnPage = value;
        displayLike(likeValueOnPage);
    };
    var err = function(errno, data) {
        console.log(data);
    };
    ajax('POST', url, args, success, err);
}

