let pagesFormPage  = document.querySelector('.page-admin-pages-edit');
let blocksFormPage = document.querySelector('.page-admin-blocks-edit');
let blockSnippet   = document.getElementById('block-snippet');

if (pagesFormPage) {
    categorySelector('language');
    copyValue('title', 'path_key', true);
    copyValue('title', 'meta_title', false);
}

if (blocksFormPage) {
    categorySelector('language');
}

if (blockSnippet) {
    copyBlockSnippet();
}

/**
 * Copy block Snippet to clipboard
 */
function copyBlockSnippet () {
    let copyLink = document.getElementById('block-copy-snippet');
    let buttonText = copyLink.innerText;

    copyLink.addEventListener('click', function (event) {
        event.preventDefault();
        copyInClipboard(blockSnippet);
        copyLink.innerHtml = buttonText + ' &check;';
    });
}
