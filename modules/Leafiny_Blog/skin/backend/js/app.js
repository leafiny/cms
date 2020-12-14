$(document).ready(function() {
    let postsFormPage = $('.page-admin-posts-edit:first');

    if (postsFormPage.length) {
        postsFormPage.leafinyCategorySelector();

        let pathKey = postsFormPage.find('#path_key');
        if (pathKey.length) {
            pathKey.leafinyCopyValue('#title', true);
        }

        let metaTitle = postsFormPage.find('#meta_title');
        if (metaTitle.length) {
            metaTitle.leafinyCopyValue('#title', false);
        }
    }
});