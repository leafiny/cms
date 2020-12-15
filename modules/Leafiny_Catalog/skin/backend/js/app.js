let productFormPage = document.querySelector('.page-admin-products-edit');

if (productFormPage) {
    categorySelector('language');
    copyValue('name', 'path_key', true);
    copyValue('name', 'meta_title', false);
}