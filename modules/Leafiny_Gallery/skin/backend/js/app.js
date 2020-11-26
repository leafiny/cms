$(document).ready(function () {
    $('.gallery-form button').leafinyGallery();
});

$.fn.leafinyGallery = function () {
    let counter = 0;

    this.click(function (event) {
        event.preventDefault();

        let maxFileNumber = $(this).data('max-file-number');
        if (maxFileNumber > 4) {
            maxFileNumber = 4;
        }

        if (counter < maxFileNumber) {
            let input = $(this).next();
            $(this).after(input.clone().val('').show());
        }
        counter++;
    });
}