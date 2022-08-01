/* Custom JavaScript */
banner();

function banner() {
    var sliderSelector = 'banner';
    var sliderHiddenClass = 'hide';
    var sliderInterval = 4000;

    var sliders = document.getElementsByClassName(sliderSelector);

    for (var i = 0; i < sliders.length; i++) {
        setInterval((function (e) {
            var item = 0;
            var slider = sliders[e];

            return function() {
                slider.children[item].classList.add(sliderHiddenClass);
                item++;
                if (item >= slider.children.length) {
                    item = 0;
                }
                slider.children[item].classList.remove(sliderHiddenClass);
            }
        })(i), sliderInterval);
    }
}
