commerceProceedStep();

/**
 * Step Cart
 */
function commerceCart() {
    updateItem();

    function updateItem() {
        let items = document.getElementsByClassName('update_item');

        if (!items) {
            return false;
        }

        for (let i = 0; i < items.length; i++) {
            items[i].addEventListener('change', function () {
                this.closest('form').submit();
            });
        }

        return true;
    }
}

/**
 * Step Addresses
 */
function commerceAddresses() {
    sameAsShipping();

    function sameAsShipping() {
        let sameAsShipping = document.getElementById('same_as_shipping');
        let billingForm = document.getElementById('billing_form');

        if (!sameAsShipping || !billingForm) {
            return false;
        }

        sameAsShipping.addEventListener('click', function () {
            if (this.checked) {
                billingForm.style.display = 'none';
                toggleRequired(false);
            } else {
                billingForm.style.display = 'block';
                toggleRequired(true);
            }
        });

        if (!sameAsShipping.checked) {
            billingForm.style.display = 'block';
            toggleRequired(true);
        }

        return true;
    }

    function toggleRequired(isRequired) {
        let inputs = document.getElementsByClassName('field-required');

        if (!inputs.length) {
            return false;
        }

        for (let i = 0; i < inputs.length; i++) {
            inputs[i].required = isRequired;
        }
    }
}

/**
 * Step shipping
 */
function commerceShipping() {

}

/**
 * Step Payment
 */
function commercePayment() {
    paymentInfo();

    function paymentInfo() {
        let inputs = document.getElementsByClassName('payment-method-input');

        if (!inputs.length) {
            return false;
        }

        for (let i = 0; i < inputs.length; i++) {
            inputs[i].addEventListener('click', function () {
                toggleInfo(this);
            });
            if (inputs[i].checked) {
                toggleInfo(inputs[i]);
            }
        }

        return true;
    }

    function toggleInfo(element) {
        let infos  = document.getElementsByClassName('payment-method-info');
        if (!infos.length) {
            return false;
        }

        for (let i = 0; i < infos.length; i++) {
            infos[i].style.display = 'none';
        }

        let info = document.getElementById('method_info_' + element.id);
        if (info) {
            info.style.display = 'block';
        }

        return true;
    }
}

/**
 * Step Review
 */
function commerceReview() {
    coupon();
    agreements();

    function coupon() {
        let link = document.getElementById('coupon_link');
        let form = document.getElementById('coupon_form');

        if (!link || !form) {
            return false;
        }

        link.addEventListener('click', function (event) {
            event.preventDefault();
            if (form.style.display === 'block') {
                form.style.display = 'none';
            } else {
                form.style.display = 'block';
            }
        });

        return true;
    }

    function agreements() {
        let form = document.getElementById('review_form');
        let agreements = document.getElementById('agreements');

        if (!form || !agreements) {
            return false
        }

        agreements.addEventListener('click', function () {
            agreements.parentNode.className = '';
        });

        form.addEventListener('submit', function (event) {
            if (!agreements.checked) {
                agreements.parentNode.className = 'error';
                event.preventDefault();
            }
        });

        return true;
    }
}

function commerceProceedStep(step = '', functionPrefix = 'commerce') {
    if (!step) {
        let progress = document.getElementById('checkout-progress');
        if (progress) {
            step = progress.getAttribute('data-step');
        }
    }

    try {
        window[functionName(step)]();
    } catch (error) {
        console.warn('Invalid function: ' + functionName(step));
    }

    function functionName(step) {
        return functionPrefix + step.charAt(0).toUpperCase() + step.toLowerCase().slice(1);
    }
}