/**
 * Copy element text in clipboard
 *
 * @param {Object} element
 */
function copyInClipboard (element) {
    element.select();
    document.execCommand('copy');
}

/**
 * Copy value from input to another input
 *
 * @param {string} fromElementId
 * @param {string} toElementId
 * @param {boolean}   normalize
 *
 * @returns {boolean}
 */
function copyValue (fromElementId, toElementId, normalize) {
    let toElement = document.getElementById(toElementId);
    let fromElement = document.getElementById(fromElementId);

    if (!toElement || !fromElement) {
        return false;
    }

    if (toElement.value) {
        return false;
    }

    fromElement.addEventListener('change', function () {
        let value = this.value;
        if (normalize) {
            value = removeAccents(value).toLowerCase().replace(/[\W_]+/g, '-');
        }
        toElement.value = value;
    });

    return true;
}

/**
 * Remove accents in string
 *
 * @param {string} str
 *
 * @returns {string}
 */
function removeAccents (str) {
    let map = {
        'a':'á|à|ã|â|À|Á|Ã|Â',
        'e':'é|è|ê|É|È|Ê',
        'i':'í|ì|î|Í|Ì|Î',
        'o':'ó|ò|ô|õ|Ó|Ò|Ô|Õ',
        'u':'ú|ù|û|ü|Ú|Ù|Û|Ü',
        'c':'ç|Ç',
        'n':'ñ|Ñ'
    };

    for (let pattern in map) {
        str = str.replace(new RegExp(map[pattern], 'g'), pattern);
    }

    return str;
}
