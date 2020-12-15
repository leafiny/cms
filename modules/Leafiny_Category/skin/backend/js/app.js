$(document).ready(function() {
    let categoryFormPage = $('.page-admin-categories-edit:first');
    if (categoryFormPage.length) {
        categoryFormPage.leafinyCategorySelector();

        copyValue('name', 'path_key', true);
        copyValue('name', 'meta_title', false);
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