$(document).ready(function () {
    let productFormPage  = $('.page-admin-products-edit:first');

    if (productFormPage.length) {
        productFormPage.leafinyCategorySelector();

        copyValue('name', 'path_key', true);
        copyValue('name', 'meta_title', false);
    }
});