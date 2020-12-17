/**
 * Product Gallery
 *
 * @returns {boolean}
 */
function productGallery() {
    let gallery = document.getElementById('product-gallery');
    if (!gallery) {
        return false;
    }

    let links = gallery.querySelectorAll('a');
    let image = document.getElementById('product-image');
    if (!image || !links.length) {
        return false;
    }

    for (let i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function (event) {
            event.preventDefault();
            image.querySelector('img').src = links[i].getAttribute('data-image');
        });
    }

    return true;
}
