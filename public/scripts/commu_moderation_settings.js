document.addEventListener('DOMContentLoaded', function() {
    const modContainer = document.getElementById("modContainer");
    const btnModeration = document.getElementById("btnModeration");
    const closeModContainer = document.getElementById("closeModContainer");
    
    const gestionAdhesionContainer = document.getElementById("gestionAdhesionContainer");
    const gestionadhesion = document.getElementById("gestionadhesion");
    const closeGestionAdhesionContainer = document.getElementById("closeGestionAdhesionContainer");
    
    const demandeadh = document.getElementById("demandeadh");
    const refusadh = document.getElementById("refusadh");
    const demandeblock = document.getElementById("demandeblock");
    const refusblock = document.getElementById("refusblock");
    
    if (btnModeration) {
        btnModeration.onclick = function() {
            window.location.hash = "modContainer";
        };
    }
    
    if (closeModContainer) {
        closeModContainer.onclick = function() {
            history.pushState("", document.title, window.location.pathname + window.location.search);
            showModalBasedOnHash();
        };
    }
    
    if (gestionadhesion) {
        gestionadhesion.onclick = function() {
            window.location.hash = "gestionAdhesionContainer";
        };
    }
    
    if (closeGestionAdhesionContainer) {
        closeGestionAdhesionContainer.onclick = function() {
            window.location.hash = "modContainer";
        };
    }
    
    if (demandeadh && refusadh) {
        demandeadh.onclick = function() {
            demandeblock.style.display = "block";
            refusblock.style.display = "none";
            demandeadh.style.textDecoration = "underline";
            demandeadh.style.textDecorationColor = "red";
            demandeadh.style.color = "red";
            refusadh.style.textDecoration = "none";
            refusadh.style.textDecorationColor = "none";
            refusadh.style.color = "black";
        };
        
        refusadh.onclick = function() {
            demandeblock.style.display = "none";
            refusblock.style.display = "block";
            refusadh.style.textDecoration = "underline";
            refusadh.style.textDecorationColor = "red";
            refusadh.style.color = "red";
            demandeadh.style.textDecoration = "none";
            demandeadh.style.textDecorationColor = "none";
            demandeadh.style.color = "black";
        };
    }
    
    function showModalBasedOnHash() {
        const hash = window.location.hash;

        if (modContainer) modContainer.style.display = "none";
        if (gestionAdhesionContainer) gestionAdhesionContainer.style.display = "none";
        
        switch(hash) {
            case "#modContainer":
                if (modContainer) modContainer.style.display = "block";
                break;
            case "#gestionAdhesionContainer":
                if (gestionAdhesionContainer) gestionAdhesionContainer.style.display = "block";
                break;
            default:
                // Aucun modal à afficher
                break;
        }
    }
    
    window.addEventListener("hashchange", showModalBasedOnHash);
    
    showModalBasedOnHash();
});