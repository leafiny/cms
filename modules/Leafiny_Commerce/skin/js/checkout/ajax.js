commerceCheckoutAjax();

function commerceCheckoutAjax() {
    saveStepTrigger();
    viewStepTrigger();

    function viewStepTrigger() {
        let viewStep = document.getElementsByClassName('view-step');
        for (let i = 0; i < viewStep.length; i++) {
            viewStep[i].addEventListener('click', function (event) {
                event.preventDefault();
                updateStep(this.href, null);
            });
        }
    }

    function saveStepTrigger() {
        let saveStep = document.getElementsByClassName('save-step');
        for (let i = 0; i < saveStep.length; i++) {
            saveStep[i].addEventListener('submit', function (event) {
                event.preventDefault();
                if (typeof event.submitter !== 'undefined') {
                    event.submitter.disabled = true;
                    event.submitter.className = event.submitter.className + ' load';
                }
                updateStep(this.action, new FormData(this));
            });
        }
    }

    function updateStep(url, form) {
        let xhr = new XMLHttpRequest();
        url = url.substring(0, url.indexOf('#')) + '&is_ajax=1';
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                let result = JSON.parse(xhr.responseText);
                if (result.redirect) {
                    window.location.replace(result.redirect);
                } else {
                    active(result.step);
                    update(result.step, result.content);
                    scrollTo(result.step);
                    commerceProceedStep(result.step);
                    saveStepTrigger();
                }
            }
        };
        xhr.open(form ? 'POST' : 'GET', url);
        xhr.send(form);

        function update(step, content) {
            let stepContent = document.getElementsByClassName('step-content');
            for (let i = 0; i < stepContent.length; i++) {
                stepContent[i].innerHTML = '';
            }
            document.getElementById('step_content_' + step).innerHTML = content;
        }

        function active(step) {
            let stepActive = document.getElementsByClassName('step');
            for (let i = 0; i < stepActive.length; i++) {
                stepActive[i].className = stepActive[i].className.replace(' active', '');
            }
            document.getElementById(step).className += ' active';
        }

        function scrollTo(element) {
            let to = document.getElementById(element);
            if (to) {
                window.scroll({
                    behavior: 'smooth',
                    left: 0,
                    top: to.offsetTop + 200,
                });
            }
        }
    }
}
