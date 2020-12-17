/**
 * Check or un-check all lines when input in table head is checked
 *
 * @returns {boolean}
 */
function gridSelectAll () {
    let select = document.querySelector('.select-all');

    if (!select) {
        return false;
    }

    select.addEventListener('click', function () {
        let inputs = document.querySelectorAll('.select-id');
        let checked = false;
        if (select.checked) {
            checked = true;
        }
        for (let i = 0; i < inputs.length; i++) {
            inputs[i].checked = checked;
        }
    });

    return true;
}

/**
 * Auto-select filter option in toolbar when a filter is updated
 *
 * @returns {boolean}
 */
function gridFilter () {
    let filters = document.querySelector('.filters');
    let action = document.getElementById('toolbar-select-action');

    if (!filters || !action) {
        return false;
    }

    let selects = filters.querySelectorAll('select, input');
    for (let i = 0; i < selects.length; i++) {
        selects[i].addEventListener('change', function () {
            action.selectedIndex = 0;
            if (selects[i].tagName === 'SELECT') {
                document.querySelector('.container-grid').submit();
            }
        });
    }

    return true;
}

/**
 * Hide or show user resources in user form
 *
 * @returns {boolean}
 */
function userResources () {
    let userIsAdmin = document.getElementById('user-edit-is-admin');
    let userResources = document.getElementById('user-edit-resources');

    if (!userIsAdmin || !userResources) {
        return false;
    }

    let options = userIsAdmin.getElementsByTagName('option');
    for (let i = 0; i < options.length; i++) {
        if (options[i].selected && options[i].value === '1') {
            userResources.style.display = 'none';
        }
    }

    userIsAdmin.addEventListener('change', function () {
        if (this.value === '1') {
            userResources.style.display = 'none';
        } else {
            userResources.style.display = 'block';
        }
    });

    return true;
}

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
