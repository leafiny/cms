$(document).ready(function() {
    let pagesFormPage = $('.page-admin-pages-edit:first');
    let blocksFormPage = $('.page-admin-blocks-edit:first');

    if (pagesFormPage.length) {
        pagesFormPage.leafinyCategorySelector();

        let pathKey = pagesFormPage.find('#path_key');
        if (pathKey.length) {
            pathKey.leafinyCopyValue('#title', true);
        }

        let metaTitle = pagesFormPage.find('#meta_title');
        if (metaTitle.length) {
            metaTitle.leafinyCopyValue('#title', false);
        }
    }

    if (blocksFormPage.length) {
        blocksFormPage.leafinyCategorySelector();
    }

    let blockSnippet = $('#block-snippet');

    if (blockSnippet.length) {
        let copyLink = $('#block-copy-snippet');
        let buttonText = copyLink.text();

        copyLink.click(function (event) {
            event.preventDefault();
            blockSnippet.leafinyCopy();
            $(this).html(buttonText + ' &check;');
        });
    }

    $('#publish_date').datepicker({ dateFormat: 'yy-mm-dd' });
});
