(function () {

    /**
     * Kolibri Javascript object
     *
     * All Kolibri Javascript code should be encapsuled in this object to keep maintainability simple.
     *
     */
    Kolibri = {

        /**
         * Ace editor instance
         */
        editor: null,

        /**
         * The init method initiates all required subcomponents
         */
        init: function () {
            // Load Foundation
            $(document).foundation();
            if(typeof hljs != 'undefined') {
                // Init highlight.js
                hljs.initHighlightingOnLoad();
            }
            // Filter the title input for the add-page-form
            $("#title").on("keyup", function () {
                var text = $(this).val();
                $(this).val(text.replace(/[^A-Za-z]/g, ''));
                $(this).val(Kolibri.capitalizeFirstLetter($(this).val()));
            });
            $("#title").on("blur", function () {
                var text = $(this).val();
                $(this).val(text.replace(/[^A-Za-z]/g, ''));
                $(this).val(Kolibri.capitalizeFirstLetter($(this).val()));
            });
            // Inject the editor data to the form on submit
            $("form#update").submit(function (e) {
                $("textarea#content").val(Kolibri.editor.getValue());
                return true;
            });
            $("form#create").submit(function (e) {
                $("textarea#content").val(Kolibri.editor.getValue());
                return true;
            });
            // Check for ace editor div presence
            if ($("div#editor").length > 0) {
                Kolibri.initAce();
                jwerty.key('ctrl+alt+s', function () {
                    $("#submit").trigger('click');
                });
            }
            // Add keybinding
            jwerty.key('ctrl+alt+a', function () {
                window.location = document.getElementById('href_add').href;
            });
            if ($("a#href_edit").length > 0) {
                jwerty.key('ctrl+alt+e', function () {
                    window.location = document.getElementById('href_edit').href;
                });
            }
        },

        /**
         * Initializes the ace editor instance for a div
         */
        initAce: function () {
            Kolibri.editor = ace.edit("editor");
            Kolibri.editor.setTheme("ace/theme/chrome");
            Kolibri.editor.getSession().setMode("ace/mode/markdown");
            Kolibri.editor.getSession().setTabSize(4);
            Kolibri.editor.getSession().setUseWrapMode(true);
            Kolibri.editor.setShowPrintMargin(false);
            document.getElementById('editor').style.fontSize = '14px';
        },

        /**
         * Uses a string and returns the string with a capital letter
         *
         * @param string
         * @returns {string}
         */
        capitalizeFirstLetter: function (string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

    }

})();

$(document).ready(function () {
    Kolibri.init();
});