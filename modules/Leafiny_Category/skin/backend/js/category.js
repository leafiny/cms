function categorySelector (languageId) {
    let languageSelector   = document.getElementById(languageId);
    let categoryContainers = document.querySelectorAll('.form-category-selector');

    if (!languageSelector || !categoryContainers.length) {
        return false;
    }

    let showLanguage = function(language) {
        for (let i = 0; i < categoryContainers.length; i++) {
            categoryContainers[i].style.display = 'none';
            let select = categoryContainers[i].querySelector('select');
            if (select) {
                select.setAttribute('disabled', 'disabled');
            }
        }

        let categorySelectors = document.querySelectorAll('.form_category_' + language);

        for (let i = 0; i < categorySelectors.length; i++) {
            categorySelectors[i].style.display = 'block';
            let select = categorySelectors[i].querySelector('select');
            if (select) {
                select.removeAttribute('disabled');
            }
        }
    };

    showLanguage(languageSelector.value);

    languageSelector.addEventListener('change', function (event) {
        showLanguage(this.value);
    });
}