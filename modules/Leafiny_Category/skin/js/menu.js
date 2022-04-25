categoryMenu();

function categoryMenu () {
    var hasChildren = document.getElementsByClassName('has-children');
    for (var i = 0; i < hasChildren.length; i++) {
        hasChildren[i].addEventListener('click', function (event) {
            event.preventDefault();
            var childMenu = this.nextSibling;
            if (childMenu.style.display === 'block') {
                childMenu.style.display = 'none';
            } else {
                childMenu.style.display = 'block';
            }
        });

        hasChildren[i].addEventListener('mouseout', function () {
            this.nextSibling.style.removeProperty('display');
        });
    }
}
