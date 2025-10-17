document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("ParamCommuContainer");
    const button = document.getElementById("btnGestion");
    const close_button = document.getElementById("closeParamCommuContainer");
    
    button.onclick = function(){
        window.location.hash = "ParamCommuContainer";
    }
    
    close_button.onclick = function(){
        history.pushState("", document.title, window.location.pathname + window.location.search);
        showModalBasedOnHash();
    }
    
    const modaux = [
        document.getElementById("modalMod"),
        document.getElementById("modalRename"),
        document.getElementById("modalDelete")
    ];
    
    const buttons = [
        document.getElementById("btnMod"),
        document.getElementById("btnRename"),
        document.getElementById("btnDelete")
    ];
    
    const close_buttons = [
        document.getElementById("closeModalMod"),
        document.getElementById("closeModalRename"),
        document.getElementById("closeModalDelete")
    ];
    
    for(let i = 0; i < modaux.length; i++){
        if (buttons[i] && modaux[i] && close_buttons[i]) {
            buttons[i].onclick = function(){
                window.location.hash = modaux[i].id;
            }
            close_buttons[i].onclick = function(){
                window.location.hash = "ParamCommuContainer";
            }
        }
    }
    
    function showModalBasedOnHash() {
        const hash = window.location.hash;
        
        if (modal) modal.style.display = "none";
        for(let i = 0; i < modaux.length; i++) {
            modaux[i].style.display = "none";
        }
        
        switch(hash) {
            case "#ParamCommuContainer":
                modal.style.display = "block";
                break;
            case "#modalMod":
                modaux[0].style.display = "block";
                break;
            case "#modalRename":
                modaux[1].style.display = "block";
                break;
            case "#modalDelete":
                modaux[2].style.display = "block";
                break;
            default:
                // Aucun modal à afficher
                break;
        }
    }

    window.addEventListener("hashchange", showModalBasedOnHash);
    
    showModalBasedOnHash();
});