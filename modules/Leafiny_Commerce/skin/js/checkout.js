window.commerceSteps = {
    "cart": ['commerceCart'],
    "addresses": ['commerceAddresses'],
    "shipping": [],
    "payment": ['commercePayment'],
    "review": ['commerceReview']
}

window.dispatchEvent(new Event('commerceSteps'));

commerceProceedStep();

/**
 * Step Cart
 */
function commerceCart() {
    updateItem();

    function updateItem() {
        var items = document.getElementsByClassName('update_item');
        if (!items) {
            return false;
        }

        for (var i = 0; i < items.length; i++) {
            items[i].addEventListener('change', function () {
                this.form.submit();
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
        var sameAsShipping = document.getElementById('same_as_shipping');
        var billingForm = document.getElementById('billing_form');

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
        var inputs = document.getElementsByClassName('field-required');

        if (!inputs.length) {
            return false;
        }

        for (var i = 0; i < inputs.length; i++) {
            inputs[i].required = isRequired;
        }
    }
}

/**
 * Step Payment
 */
function commercePayment() {
    paymentInfo();

    function paymentInfo() {
        var inputs = document.getElementsByClassName('payment-method-input');

        if (!inputs.length) {
            return false;
        }

        for (var i = 0; i < inputs.length; i++) {
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
        var infos  = document.getElementsByClassName('payment-method-info');
        if (!infos.length) {
            return false;
        }

        for (var i = 0; i < infos.length; i++) {
            infos[i].style.display = 'none';
        }

        var info = document.getElementById('method_info_' + element.id);
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
    agreements();

    function agreements() {
        var form = document.getElementById('review_form');
        var agreements = document.getElementById('agreements');

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

function commerceProceedStep(step) {
    if (typeof step === 'undefined') {
        var progress = document.getElementById('checkout-progress');
        if (progress) {
            step = progress.getAttribute('data-step');
        }
    }

    if (step) {
        var functions = window.commerceSteps[step];
        if (typeof functions !== 'undefined') {
            for (var i = 0; i < functions.length; i++) {
                try {
                    window[functions[i]]();
                } catch (error) {
                    console.warn('Invalid function: ' + step + '::' + functions[i]);
                }
            }
        }
    }
}
