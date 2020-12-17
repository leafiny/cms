userResources();

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
