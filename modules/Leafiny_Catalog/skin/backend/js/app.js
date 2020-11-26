$(document).ready(function () {
    let productFormPage  = $('.page-admin-products-edit:first');

    if (productFormPage.length) {
        productFormPage.leafinyCategorySelector();
    }
});