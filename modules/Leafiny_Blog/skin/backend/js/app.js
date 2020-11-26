$(document).ready(function() {
    let postsFormPage = $('.page-admin-posts-edit:first');

    if (postsFormPage.length) {
        postsFormPage.leafinyCategorySelector();
    }
});