$(document).ready(function() {
    let categoryFormPage = $('.page-admin-categories-edit:first');
    if (categoryFormPage.length) {
        categoryFormPage.leafinyCategorySelector();

        let pathKey = categoryFormPage.find('#path_key');
        if (pathKey.length) {
            pathKey.leafinyCopyValue('#name', true);
        }

        let metaTitle = categoryFormPage.find('#meta_title');
        if (metaTitle.length) {
            metaTitle.leafinyCopyValue('#name', false);
        }
    }
});

$.fn.leafinyCategorySelector = function () {
    let languageSelector   = this.find('#language');
    let categoryContainers = $('.form-category-selector');

    let showLanguage = function(language) {
        categoryContainers.hide();
        categoryContainers.find('select').attr('disabled', 'disabled');

        let categorySelector = $('.form_category_' + language);

        categorySelector.each(function () {
            $(this).show();
            $(this).find('select').removeAttr('disabled');
        });
    };

    showLanguage(languageSelector.val());

    languageSelector.change(function () {
        showLanguage($(this).val());
    });
}