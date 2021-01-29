goTo();

/**
 * Form go to
 *
 * @returns {boolean}
 */
function goTo () {
    let goTo = document.getElementById('form-go-to');
    if (!goTo) {
        return false;
    }

    let legends = document.getElementsByTagName('legend');
    if (!legends.length) {
        return false;
    }

    let select = document.getElementById('select-go-to');
    select.addEventListener('change', function () {
        if (this.value) {
            location.hash = this.value;
        }
    });

    let option = document.createElement('option');
    select.appendChild(option);

    for (let i = 0; i < legends.length; i++) {
        let legendId = 'section-' + removeAccents(legends[i].innerHTML).toLowerCase().replace(/[\W_]+/g, '-');

        legends[i].id = legendId;

        let option = document.createElement('option');
        option.value = '#' + legendId;
        option.text = legends[i].innerHTML;

        select.appendChild(option);
    }

    return true;
}