productGallery();
responsiveMenu();

/**
 * Menu display on small screen devices
 *
 * @returns {boolean}
 */
function responsiveMenu() {
    let categories =  document.getElementById('menu-categories');
    let mobileMenuLink = document.getElementById('menu-mobile-link');
    if (!mobileMenuLink || !categories) {
        return false;
    }

    let isSmallScreen = false;

    mobileMenuLink.onclick = function () {
        isSmallScreen = true;
        this.classList.toggle('open');
        if (!categories.style.display || categories.style.display === 'none') {
            categories.style.display = 'inline-block'
        } else {
            categories.style.display = 'none'
        }
        return false;
    }

    let links = categories.querySelectorAll('.pure-menu-link');
    Array.prototype.forEach.call(links, function (element) {
        element.onclick = function () {
            if (isSmallScreen && this.nextSibling) {
                let subMenu = this.nextSibling;
                if (!subMenu.style.display || subMenu.style.display === 'none') {
                    subMenu.style.display = 'inline-block'
                } else {
                    subMenu.style.display = 'none'
                }
                return false;
            }
        }
    });

    return true;
}

/**
 * Product Gallery on product page
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

    Array.prototype.forEach.call(links, function (element) {
        element.onclick = function () {
            image.querySelector('img').src = element.getAttribute('data-image');
            return false;
        }
    });

    return true;
}