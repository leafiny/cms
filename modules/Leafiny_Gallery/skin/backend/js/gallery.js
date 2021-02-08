gallery();

/**
 * Init gallery form if needed
 *
 * @returns {boolean}
 */
function gallery () {
    let galleryForm = document.querySelector('.gallery-form button');

    if (!galleryForm) {
        return false;
    }

    let counter = 0;

    galleryForm.addEventListener('click', function (event) {
        event.preventDefault();

        let maxFileNumber = this.getAttribute('data-max-file-number');
        if (maxFileNumber > 4) {
            maxFileNumber = 4;
        }

        if (counter < maxFileNumber) {
            let input = this.nextSibling;
            if (input) {
                let newInput = input.cloneNode(false);
                newInput.value = '';
                newInput.style.display = 'block';
                this.after(newInput);
            }
        }
        counter++;
    });

    let galleryImages = document.getElementsByClassName('gallery-image');

    if (!galleryImages) {
        return false;
    }

    for (let i = 0; i < galleryImages.length; i++) {
        let element = galleryImages[i];

        let width = element.getAttribute('data-width');
        let height = element.getAttribute('data-height');

        let size = element.offsetWidth;

        if (width && height && width < size && height < size) {
            element.className = element.className + ' auto';
        }
    }

    return true;
}
