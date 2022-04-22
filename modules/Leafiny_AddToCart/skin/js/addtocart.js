addToCartAjax();

/**
 * Add product to cart with Ajax
 */
function addToCartAjax () {
    let addToCart = document.getElementsByClassName('product-add-to-cart');

    for (let i = 0; i < addToCart.length; i++) {
        addToCart[i].addEventListener('submit', function (event) {
            event.preventDefault();
            let form = this;
            let url = this.action.replace('/add/', '/ajax/add/');
            let xhr = new XMLHttpRequest();
            loader(form, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    loader(form, false);

                    let result = JSON.parse(xhr.responseText);

                    let minicart = document.getElementById('minicart');
                    if (result.hasOwnProperty('minicart') && minicart) {
                        replaceWith(minicart, result.minicart);
                    }

                    if (result.hasOwnProperty('popup')) {
                        popup(result.popup);
                    }

                    if (result.hasOwnProperty('error')) {
                        alert(result.error);
                    }
                }
            }
            xhr.open('POST', url);
            xhr.send(new FormData(event.target));
        });
    }

    /**
     * Add loader to button
     *
     * @param {Object} form
     * @param {boolean} active
     * @returns {boolean}
     */
    function loader (form, active) {
        let button = form.getElementsByTagName('button');
        if (!button.length) {
            return false;
        }
        if (active) {
            button[0].disabled = true;
            button[0].className += ' load';
        }
        if (!active) {
            button[0].disabled = false;
            button[0].className = button[0].className.replace(' load', '');
        }

        return true;
    }

    /**
     * Replace element with HTML
     *
     * @param {Node} source
     * @param {string} target
     */
    function replaceWith (source, target)
    {
        let element = document.createElement('div');
        element.innerHTML = target;

        source.parentNode.replaceChild(element.firstChild, source);
    }

    /**
     * Open new popup
     *
     * @param {string} html
     */
    function popup (html)
    {
        close();

        let container = document.createElement('div');
        container.className = 'cart-popup-container';
        container.addEventListener('click', function () {
            close();
        });

        let content = document.createElement('div');
        content.className = 'cart-popup-content';
        content.innerHTML = html;

        let closeAction = content.getElementsByClassName('close');
        for (let i = 0; i < closeAction.length; i++) {
            closeAction[i].addEventListener('click', function (event) {
                event.preventDefault();
                close();
            });
        }

        document.body.prepend(container);
        document.body.prepend(content);

        let popup = document.getElementsByClassName('cart-popup-content');
        setTimeout(function() {
            popup[0].className += ' show';
        }, 50);

        /**
         * Close popup
         */
        function close ()
        {
            let containers = document.getElementsByClassName('cart-popup-container');
            for (let i = 0; i < containers.length; i++) {
                containers[i].remove();
            }

            let contents = document.getElementsByClassName('cart-popup-content');
            for (let i = 0; i < contents.length; i++) {
                contents[i].remove();
            }
        }
    }
}