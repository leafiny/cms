let postsFormPage = document.querySelector('.page-admin-posts-edit');

if (postsFormPage) {
    categorySelector('language');
    copyValue('title', 'path_key', true);
    copyValue('title', 'meta_title', false);
}