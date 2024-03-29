formGoTo();
formFieldDepends();
countFieldChars();

/**
 * Count chars in field
 *
 * @returns {boolean}
 */
function countFieldChars() {
    let fields = document.getElementsByClassName('count-chars');
    if (!fields.length) {
        return false;
    }

    for (let i = 0; i < fields.length; i++) {
        let inputs = fields[i].querySelectorAll('input, textarea');
        for (let j = 0; j < inputs.length; j++) {
            update(fields[i], inputs[j].value.length);
            inputs[j].addEventListener('keyup', function () {
                update(fields[i], this.value.length);
            });
        }
    }

    function update(parent, length) {
        let chars = parent.querySelector('.chars');
        if (!chars) {
            chars = document.createElement('span');
            chars.className = 'chars';
            parent.appendChild(chars);
        }

        chars.innerHTML = length;

        if (!length) {
            chars.remove();
        }
    }
}

/**
 * Form go to
 *
 * @returns {boolean}
 */
function formGoTo() {
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
        let legendId =
            'section-' +
            removeAccents(legends[i].innerHTML)
                .toLowerCase()
                .replace(/[\W_]+/g, '-');

        legends[i].id = legendId;

        let option = document.createElement('option');
        option.value = '#' + legendId;
        option.text = legends[i].innerHTML;

        select.appendChild(option);
    }

    return true;
}

/**
 * Add dependencies between fields
 *
 * <div class="pure-u-1 field depends" data-parent="parent_id" data-values="john,peter,alex">...</div>
 */
function formFieldDepends() {
    let depends = document.getElementsByClassName('depends');

    if (depends.length) {
        for (let i = 0; i < depends.length; i++) {
            let parentId = depends[i].getAttribute('data-parent');
            let values = depends[i].getAttribute('data-values');

            let parent = document.getElementById(parentId);

            if (parent) {
                toggle(depends[i], parent, values);

                parent.addEventListener('change', function () {
                    toggle(depends[i], this, values);
                });
            }
        }
    }

    /**
     * Toggle field
     *
     * @param {Object} element
     * @param {Object} parent
     * @param {string} values
     */
    function toggle(element, parent, values) {
        element.style.display = 'none';
        if (values.split(',').indexOf(parent.value) >= 0) {
            element.style.display = 'block';
        }
    }
}
