var img = document.createElement("img");
$("#pfp").change(function(e) {

    for (var i = 0; i < e.originalEvent.srcElement.files.length; i++) {

        var file = e.originalEvent.srcElement.files[i];

        img.className = "displayedImage";
        var reader = new FileReader();
        reader.onloadend = function() {
             img.src = reader.result;
        }
        reader.readAsDataURL(file);
        $("#pfp").after(img);
    }
});