$(document).ready(function() {
    $('.container-grid').leafinyGridFilter();
    $('.select-all').leafinyGridSelectAll();
    $('#user-edit-is-admin').leafinyUserResources();
    $('#admin-menu-button').leafinyResponsiveOpenMenu();
    $('#admin-menu-logo').leafinyResponsiveCloseMenu();
});

$.fn.leafinyGridSelectAll = function () {
    let element = this;

    element.click(function () {
        if (element.is(":checked")) {
            $('.select-id').prop( "checked", true);
        } else {
            $('.select-id').prop( "checked", false);
        }
    });
};

$.fn.leafinyGridFilter = function () {
    let filters = this.find('.filters');
    let toolbar = this.find('.toolbar');

    filters.find('select').change(function () {
        toolbar.find('option[value="filter"]').prop('selected', true);
        this.form.submit();
    });

    filters.find('input').change(function () {
        toolbar.find('option[value="filter"]').prop('selected', true);
    });
};

$.fn.leafinyUserResources = function () {
    let selected = this.children("option:selected").val();
    if (selected === '1') {
        $('#user-edit-resources').hide();
    }

    this.change(function () {
        let selected = $(this).children("option:selected").val();
        let resource = $('#user-edit-resources');

        if (selected === '1') {
            resource.hide();
        } else {
            resource.show();
        }
    });
}

$.fn.leafinyCopy = function () {
    this.select();
    document.execCommand('copy');
}

$.fn.leafinyResponsiveOpenMenu = function () {
    this.click(function (event) {
        $('nav').css({'margin-left': '0px'});
        event.preventDefault();
    });
}

$.fn.leafinyResponsiveCloseMenu = function () {
    this.click(function (event) {
        let menu = $('nav');
        if (menu.css('margin-left') === '0px') {
            menu.css({'margin-left': '-200px'});
            event.preventDefault();
        }
    });
}

$.fn.leafinyCopyValue = function (refererId, normalize) {
    let key = this;
    let referer = $(refererId);

    if (!key.val() && referer.length) {
        referer.change(function () {
            let value = $(this).val();
            if (normalize) {
                value = removeAccents($(this).val()).toLowerCase().replace(/[\W_]+/g, '-');
            }
            key.val(value);
        });
    }
}

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