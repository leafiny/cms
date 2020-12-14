$(document).ready(function () {
    let productFormPage  = $('.page-admin-products-edit:first');

    if (productFormPage.length) {
        productFormPage.leafinyCategorySelector();

        let pathKey = productFormPage.find('#path_key');
        if (pathKey.length) {
            pathKey.leafinyCopyValue('#name', true);
        }

        let metaTitle = productFormPage.find('#meta_title');
        if (metaTitle.length) {
            metaTitle.leafinyCopyValue('#name', false);
        }
    }
});