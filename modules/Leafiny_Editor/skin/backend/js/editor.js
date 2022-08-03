let editors = document.querySelectorAll('.editor');

for (let i = 0; i < editors.length; i++) {
    runEditor(editors[i]);
}

function runEditor(editor) {
    let converter = new showdown.Converter();

    let $ = editor.querySelector.bind(editor);
    let $$ = editor.querySelectorAll.bind(editor);

    let showContent = function (editor, className) {
        let editorContents = $$('.editor-content');

        for (let i = 0; i < editorContents.length; i++) {
            editorContents[i].style.display = 'none';
        }

        $('.' + className).style.display = 'block';
    };

    let convert = function (content) {
        if (getType(content) === 'markdown') {
            return converter.makeHtml(content.value);
        }
        if (getType(content) === 'html') {
            return cleanMarkdown(converter.makeMarkdown(content.value));
        }
    };

    let getType = function (element) {
        if (element.classList.contains('editor-markdown')) {
            return 'markdown';
        }
        if (element.classList.contains('editor-html')) {
            return 'html';
        }

        return 'preview';
    };

    let cleanMarkdown = function (value) {
        value = value.replace(/<!-- -->/g, '');
        value = value.replace(/\n\s*\n/g, '\n\n');

        return value;
    };

    let editorActions = $$('.editor-action');

    for (let i = 0; i < editorActions.length; i++) {
        if (i === 0) {
            showContent(editor, editorActions[i].id);
            editorActions[i].classList.add('active');
        }
        editorActions[i].addEventListener('click', function (event) {
            event.preventDefault();
            for (let j = 0; j < editorActions.length; j++) {
                editorActions[j].classList.remove('active');
            }
            this.classList.add('active');
            showContent(editor, editorActions[i].id);
        });
    }

    let editorContents = $$('.editor-content');

    for (let i = 0; i < editorContents.length; i++) {
        let type = getType(editorContents[i]);
        let html = $('.editor-html');

        if (type === 'markdown') {
            editorContents[i].value = convert(html);
        }
        if (type === 'preview') {
            editorContents[i].innerHTML = html.value;
        }

        editorContents[i].addEventListener('change', function () {
            let type = getType(editorContents[i]);
            let value = convert(editorContents[i]);

            if (type === 'markdown') {
                $('.editor-html').value = value;
                $('.editor-preview').innerHTML = value;
            }
            if (type === 'html') {
                $('.editor-markdown').value = value;
                $('.editor-preview').innerHTML = this.value;
            }
        });
    }
}
