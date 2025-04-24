document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("creerDiscussionContainer");
    const button = document.getElementById("btnCreerDiscussion");
    const close_button = document.getElementById("closeDiscussionContainer");

    if (button && modal && close_button) {
        button.onclick = function() {
            window.location.hash = "creerDiscussionContainer";
        }

        close_button.onclick = function() {
            history.pushState("", document.title, window.location.pathname + window.location.search);
            showModalBasedOnHash();
        }
    }

    function showModalBasedOnHash() {
        const hash = window.location.hash;

        if (modal) {
            if (hash === "#creerDiscussionContainer") {
                modal.style.display = "block";
            } else {
                modal.style.display = "none";
            }
        }
    }

    window.addEventListener("hashchange", showModalBasedOnHash);
    showModalBasedOnHash();
});
