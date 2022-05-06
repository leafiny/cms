categoryMenu();

function categoryMenu () {
    var hasChildren = document.getElementsByClassName('has-children');
    for (var i = 0; i < hasChildren.length; i++) {
        const childMenu = hasChildren[i].nextSibling;
        if (childMenu) {
            hasChildren[i].addEventListener('click', function (event) {
                event.preventDefault();
                if (childMenu.style.display === 'block') {
                    childMenu.style.display = 'none';
                } else {
                    childMenu.style.display = 'block';
                }
            });

            childMenu.addEventListener('mouseout', function () {
                this.style.removeProperty('display');
            });
        }
    }
}
