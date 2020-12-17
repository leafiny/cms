gridSelectAll();
gridFilter();

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
