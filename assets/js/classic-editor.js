(function () {
    "use strict";

    var i18n = cgData.i18n;

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                           */
    /* ------------------------------------------------------------------ */

    function convertToHtml(text) {
        if (text.indexOf("<p>") !== -1 || text.indexOf("<h2>") !== -1 || text.indexOf("<ul>") !== -1) {
            return text;
        }
        var lines = text.split("\n");
        var html = "", inList = false, i, line;
        for (i = 0; i < lines.length; i++) {
            line = lines[i];
            if (line.indexOf("### ") === 0) {
                if (inList) { html += "</ul>"; inList = false; }
                html += "<h3>" + line.substring(4) + "</h3>";
            } else if (line.indexOf("## ") === 0) {
                if (inList) { html += "</ul>"; inList = false; }
                html += "<h2>" + line.substring(3) + "</h2>";
            } else if (line.indexOf("# ") === 0) {
                if (inList) { html += "</ul>"; inList = false; }
                html += "<h1>" + line.substring(2) + "</h1>";
            } else if (line.indexOf("- ") === 0 || line.indexOf("* ") === 0) {
                if (!inList) { html += "<ul>"; inList = true; }
                html += "<li>" + line.substring(2) + "</li>";
            } else if (line.trim() === "") {
                if (inList) { html += "</ul>"; inList = false; }
            } else {
                if (inList) { html += "</ul>"; inList = false; }
                html += "<p>" + line + "</p>";
            }
        }
        if (inList) { html += "</ul>"; }
        html = html.replace(/\*\*(.+?)\*\*/g, "<strong>$1</strong>");
        html = html.replace(/\*(.+?)\*/g, "<em>$1</em>");
        return html;
    }

    function showNotice(message, type) {
        var existing = document.getElementById("cg-notice");
        if (existing) existing.remove();

        var notice = document.createElement("div");
        notice.id        = "cg-notice";
        notice.className = "cg-notice cg-notice--" + type;
        notice.innerText = message;

        var target = document.getElementById("titlewrap");
        if (target) {
            target.parentNode.insertBefore(notice, target.nextSibling);
        }
        if (type !== "info") {
            setTimeout(function () { if (notice.parentNode) notice.remove(); }, 8000);
        }
    }

    function createButton() {
        var btn       = document.createElement("button");
        btn.type      = "button";
        btn.id        = "cg-generate-btn";
        btn.className = "button button-secondary cg-btn";
        btn.innerHTML =
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f0b000" stroke="#f0b000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2z"/></svg>'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="#f0b000" stroke="#f0b000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cg-btn__star-small"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2z"/></svg>'
          + ' ' + i18n.btnLabel;
        return btn;
    }

    function setButtonState(btn, loading) {
        btn.disabled = loading;
        if (loading) {
            btn.dataset.originalHtml = btn.innerHTML;
            btn.innerText = i18n.generating;
        } else if (btn.dataset.originalHtml) {
            btn.innerHTML = btn.dataset.originalHtml;
        }
    }

    /* ------------------------------------------------------------------ */
    /*  AJAX                                                              */
    /* ------------------------------------------------------------------ */

    function callAjax(title, btn, onSuccess) {
        setButtonState(btn, true);
        showNotice(i18n.connecting, "info");

        var xhr = new XMLHttpRequest();
        xhr.open("POST", cgData.ajaxUrl, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            setButtonState(btn, false);
            if (xhr.status === 200) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        showNotice(i18n.generated, "success");
                        onSuccess(res.data.content);
                    } else {
                        showNotice(res.data && res.data.message ? res.data.message : i18n.errorUnknown, "error");
                    }
                } catch (e) {
                    showNotice(i18n.errorResponse, "error");
                }
            } else {
                showNotice("HTTP " + xhr.status, "error");
            }
        };

        xhr.onerror = function () {
            setButtonState(btn, false);
            showNotice(i18n.errorConnection, "error");
        };

        xhr.send(
            "action=sacg_generate_content"
            + "&nonce="      + encodeURIComponent(cgData.nonce)
            + "&title="      + encodeURIComponent(title)
            + "&post_type="  + encodeURIComponent(cgData.postType)
        );
    }

    /* ------------------------------------------------------------------ */
    /*  Init                                                              */
    /* ------------------------------------------------------------------ */

    function addButton() {
        if (document.getElementById("cg-generate-btn")) return true;

        var titlewrap = document.getElementById("titlewrap");
        if (!titlewrap) return false;

        var btn = createButton();
        titlewrap.parentNode.insertBefore(btn, titlewrap.nextSibling);

        btn.addEventListener("click", function () {
            var titleEl = document.getElementById("title");
            var title   = titleEl ? titleEl.value.trim() : "";
            var content = "";

            if (typeof tinymce !== "undefined" && tinymce.get("content")) {
                content = tinymce.get("content").getContent().replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim();
            } else {
                var ta = document.getElementById("content");
                content = ta ? ta.value.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : "";
            }

            if (!title) {
                alert(i18n.alertNoTitle);
                return;
            }
            if (content !== "" && !confirm(i18n.confirmReplace)) {
                return;
            }

            callAjax(title, btn, function (text) {
                var html = convertToHtml(text);

                if (typeof tinymce !== "undefined" && tinymce.get("content")) {
                    var editor = tinymce.get("content");
                    editor.setContent(html);
                    editor.save();

                    // If WP Bakery is active, force re-render to update the content in the builder
                    if (typeof window.vc !== "undefined" && window.vc.events) {
                        window.vc.events.trigger("vc:backend_editor:switch");
                    }
                } else {
                    var ta = document.getElementById("content");
                    if (ta) ta.value = html;
                }
            });
        });

        return true;
    }

    // Only initialise if this post type has a prompt configured
    if ( ! cgData.hasPrompt ) {
        console.log("[Content Generator] No prompt configured for post type '" + cgData.postType + "'. Button hidden.");
        return;
    }

    console.log("[Content Generator] Classic editor started.");

    // Try to insert inmediatly
    if (!addButton()) {
        // If fails, use MutationObserver to detect when the DOM is ready
        // This is more reliable than setInterval with WP Bakery and other page builders
        var observer = new MutationObserver(function () {
            if (addButton()) {
                console.log("[Content Generator] Button inserted via MutationObserver.");
                observer.disconnect();
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });

        // Disconnect the observer after 30 seconds
        setTimeout(function () {
            observer.disconnect();
            console.log("[Content Generator] MutationObserver disconnected after timeout.");
        }, 30000);
    }

})();