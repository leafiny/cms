categorySelector('language');
copyBlockSnippet();

/**
 * Copy block Snippet to clipboard
 *
 * @returns {boolean}
 */
function copyBlockSnippet () {
    let copyLink = document.getElementById('block-copy-snippet');

    if (!copyLink) {
        return false;
    }

    let buttonText = copyLink.innerHTML;

    copyLink.addEventListener('click', function (event) {
        event.preventDefault();
        copyInClipboard(blockSnippet);
        copyLink.innerHTML = buttonText + ' &check;';
    });

    return true;
}
