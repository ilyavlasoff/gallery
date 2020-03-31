var input = document.getElementById('fileInput');
var preview = document.getElementById('previewImage');
var fileinfo = document.getElementById('fileInfo');
input.addEventListener('change', photoAdd);
function photoAdd()
{
    let uploadadFile = input.files;
    while (fileinfo.firstChild) {
        fileinfo.removeChild(fileinfo.firstChild);
    }
    if (uploadadFile.length !== 1) {
        let error = document.createElement('label');
        error.innerText = 'Error loading files';
        error.style.color = "red";
    } else {
        let file = uploadadFile[0];
        let filename = document.createElement('label');
        filename.innerText = 'File: ' + file.name;
        let filesize = document.createElement('label');
        filesize.innerText = 'Size: ' + file.size;
        fileinfo.appendChild(filename);
        fileinfo.appendChild(document.createElement('br'));
        fileinfo.appendChild(filesize);
        preview.src = window.URL.createObjectURL(file);
    }
}