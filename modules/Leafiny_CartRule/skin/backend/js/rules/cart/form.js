commerceCartRuleConditions();

/**
 * Cart rule condition selector
 */
function commerceCartRuleConditions() {
    let template = document.getElementById('cart-rule-condition-template');
    let conditions = document.getElementById('cart-rule-conditions');

    if (conditions && template) {
        let add = document.getElementById('cart-rule-condition-add');
        if (add) {
            add.addEventListener('click', function (event) {
                event.preventDefault();

                let element = template.cloneNode(true);
                element.innerHTML = element.innerHTML.replaceAll('{_index_}', String(new Date().getTime()));
                element.removeAttribute('id');
                element.style.display = 'block';
                orCondition(element);
                rmCondition(element);

                conditions.appendChild(element);
            });
        }

        let children = document.getElementsByClassName('cart-rule-condition');
        for (let i = 0; i < children.length; i++) {
            orCondition(children[i]);
            rmCondition(children[i]);
        }
    }

    function orCondition(element) {
        let or = element.querySelector('.cart-rule-condition-or');
        or.addEventListener('click', function (event) {
            event.preventDefault();
            let container = this.parentNode.previousSibling;
            if (container) {
                let element = container.querySelector('input').cloneNode(true);
                element.value = '';
                container.appendChild(element);
            }
        });
    }

    function rmCondition(element) {
        let rm = element.querySelector('.cart-rule-condition-rm');
        rm.addEventListener('click', function (event) {
            event.preventDefault();
            let container = this.parentNode.previousSibling.previousSibling;
            if (container) {
                let elements = container.querySelectorAll('input');
                if (elements.length === 1) {
                    let condition = container.parentNode;
                    condition.parentNode.removeChild(condition);
                } else {
                    container.removeChild(elements[elements.length - 1]);
                }
            }
        });
    }
}
