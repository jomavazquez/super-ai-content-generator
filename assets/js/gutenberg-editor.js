(function () {
    "use strict";

    var i18n = cgData.i18n;

    /* ---------------------------------------------------------------------------------------- */
    /* Wait for wp.* to be available (in case this script is loaded before Gutenberg's scripts) */
    /* ---------------------------------------------------------------------------------------- */

    function waitForWp(cb) {
        if (
            typeof wp !== "undefined" &&
            wp.data && wp.element && wp.plugins &&
            wp.editPost && wp.components && wp.blocks
        ) {
            cb();
        } else {
            setTimeout(function () { waitForWp(cb); }, 300);
        }
    }

    /* -------------------------------------------------------------------------- */
    /* Insert blocks into Gutenberg from HTML, with error handling and fallback.  */
    /* -------------------------------------------------------------------------- */

    function insertBlocksFromHtml(html) {
        try {
            var blocks = wp.blocks.rawHandler({ HTML: html });
            if (!blocks || blocks.length === 0) {
                blocks = [wp.blocks.createBlock("core/html", { content: html })];
            }
            wp.data.dispatch("core/block-editor").resetBlocks(blocks);
        } catch (e) {
            console.error("[Content Generator] Error inserting blocks:", e);
            try {
                var fallback = wp.blocks.createBlock("core/freeform", { content: html });
                wp.data.dispatch("core/block-editor").resetBlocks([fallback]);
            } catch (e2) {
                console.error("[Content Generator] Fallback also failed:", e2);
            }
        }
    }

    /* ------------------------------------------------------------------ */
    /*  AJAX                                                              */
    /* ------------------------------------------------------------------ */

    function callAjax(title, onStatusChange, onSuccess) {
        onStatusChange("info", i18n.connecting);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", cgData.ajaxUrl, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        onStatusChange("success", i18n.generated);
                        onSuccess(res.data.content);
                    } else {
                        onStatusChange("error", res.data && res.data.message ? res.data.message : i18n.errorUnknown);
                    }
                } catch (e) {
                    onStatusChange("error", i18n.errorResponse);
                }
            } else {
                onStatusChange("error", "HTTP " + xhr.status);
            }
        };

        xhr.onerror = function () {
            onStatusChange("error", i18n.errorConnection);
        };

        xhr.send(
            "action=sacg_generate_content"
            + "&nonce="     + encodeURIComponent(cgData.nonce)
            + "&title="     + encodeURIComponent(title)
            + "&post_type=" + encodeURIComponent(cgData.postType)
        );
    }

    /* ------------------------------------------------------------------ */
    /*  Componente React (panel lateral de Gutenberg)                     */
    /* ------------------------------------------------------------------ */

    function CgPanel() {
        var el        = wp.element.createElement;
        var Fragment  = wp.element.Fragment;
        var useState  = wp.element.useState;
        var useSelect = wp.data.useSelect;
        var Button    = wp.components.Button;
        var Notice    = wp.components.Notice;

        var stateArr   = useState(null);
        var status     = stateArr[0];
        var setStatus  = stateArr[1];

        var loadingArr = useState(false);
        var loading    = loadingArr[0];
        var setLoading = loadingArr[1];

        var title = useSelect(function (select) {
            return select("core/editor").getEditedPostAttribute("title");
        }, []);

        function handleClick() {
            if (!title) {
                setStatus({ type: "error", msg: i18n.errorNoTitle });
                return;
            }

            var existingBlocks = wp.data.select("core/block-editor").getBlocks();
            var hasContent = existingBlocks.length > 0 && !(
                existingBlocks.length === 1 &&
                existingBlocks[0].name === "core/paragraph" &&
                (!existingBlocks[0].attributes.content || existingBlocks[0].attributes.content.trim() === "")
            );

            if (hasContent && !window.confirm(i18n.confirmReplace)) {
                return;
            }

            setLoading(true);

            callAjax(
                title,
                function (type, msg) {
                    setStatus({ type: type, msg: msg });
                    if (type !== "info") {
                        setLoading(false);
                        if (type === "success") {
                            setTimeout(function () { setStatus(null); }, 6000);
                        }
                    }
                },
                function (html) {
                    insertBlocksFromHtml(html);
                }
            );
        }

        return el(
            Fragment,
            null,
            status && el(
                Notice,
                {
                    status:        status.type === "info" ? "info" : (status.type === "success" ? "success" : "error"),
                    isDismissible: true,
                    onRemove:      function () { setStatus(null); },
                },
                status.msg
            ),
            el(
                Button,
                {
                    variant:  "secondary",
                    onClick:  handleClick,
                    icon:     "star-filled",
                    isBusy:   loading,
                    disabled: loading,
                    style:    { width: "100%", justifyContent: "center" },
                },
                loading ? i18n.generating : i18n.btnLabel
            )
        );
    }

    /* ------------------------------------------------------------------ */
    /* Register Gutenberg plugin                                          */
    /* ------------------------------------------------------------------ */

    waitForWp(function () {
        // No prompt configured for this post type → don't register the panel
        if ( ! cgData.hasPrompt ) {
            console.log("[Content Generator] No prompt configured for post type '" + cgData.postType + "'. Panel hidden.");
            return;
        }

        var el             = wp.element.createElement;
        var registerPlugin = wp.plugins.registerPlugin;
        var PluginPanel    = wp.editPost.PluginDocumentSettingPanel;

        registerPlugin("cg-content-generator", {
            render: function () {
                return el(
                    PluginPanel,
                    { name: "cg-panel", title: i18n.panelTitle },
                    el(CgPanel, null)
                );
            },
        });

        console.log("[Content Generator] Gutenberg plugin registered.");
    });

})();