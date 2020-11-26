$(document).ready(function() {
    $('.wysiwyg').wysiwyg();
});

$.fn.wysiwyg = function () {
    this.trumbowyg({
        svgPath: trumbowygSvg,
        btns: [
            ['viewHTML'],
            ['formatting'],
            ['strong', 'em', 'del'],
            ['superscript', 'subscript'],
            ['link'],
            ['insertImage', 'upload'],
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat'],
            ['fullscreen']
        ],
        plugins: {
            upload: {
                serverPath: trumbowygUpload,
                fileFieldName: 'file',
                urlPropertyName: 'url'
            }
        }
    });
}