gridSelectAll();
gridFilter();
gridRemoveWarning();

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
 * Display a confirmation message before remove entries
 */
function gridRemoveWarning () {
    let grids = document.getElementsByClassName('container-grid');
    for (let i = 0; i < grids.length; i++) {
        grids[i].addEventListener('submit', function (event) {
            if (document.getElementById('toolbar-select-action').value === 'remove') {
                event.preventDefault();
                let inputs = this.getElementsByClassName('select-id');
                let number = 0;
                for (let j = 0; j < inputs.length; j++) {
                    if (inputs[j].checked) {
                        number++;
                    }
                }
                if (number) {
                    let confirm = modal(
                        document.getElementById('modal-remove-message').innerHTML.replace('${number}', String(number)),
                        'grid-remove'
                    );
                    document.getElementById('modal-button-1').addEventListener('click', function (event) {
                        event.preventDefault();
                        confirm.close();
                    });
                    document.getElementById('modal-button-2').addEventListener('click', function (event) {
                        event.preventDefault();
                        grids[i].submit();
                    });
                }
            }
        });
    }
}
