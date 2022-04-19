window.addEventListener('commerceSteps', function (event) {
    window.commerceSteps['review'].push('cartRuleCoupon');
});

function cartRuleCoupon() {
    coupon();

    function coupon() {
        var link = document.getElementById('coupon_link');
        var form = document.getElementById('coupon_form');

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
}
