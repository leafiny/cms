$(document).ready(function() {
    let postsFormPage = $('.page-admin-posts-edit:first');

    if (postsFormPage.length) {
        postsFormPage.leafinyCategorySelector();

        copyValue('title', 'path_key', true);
        copyValue('title', 'meta_title', false);
    }
});