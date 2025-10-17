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

    const listeAvertiContainer = document.getElementById("listeAvertiContainer");
    const gestionaverti = document.getElementById("gestionaverti"); 
    const closeListeAvertiContainer = document.getElementById("closeListeAvertiContainer");

    const listeBanniContainer = document.getElementById("listeBanniContainer");
    const gestionbanni = document.getElementById("gestionbanni"); 
    const closeListeBanniContainer = document.getElementById("closeListeBanniContainer");
    
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

    if (gestionaverti) {
        gestionaverti.onclick = function() {
            window.location.hash = "listeAvertiContainer";
        };
    }

    if (closeListeAvertiContainer) {
        closeListeAvertiContainer.onclick = function() {
            window.location.hash = "modContainer";
        };
    }

    if (gestionbanni) {
        gestionbanni.onclick = function() {
            window.location.hash = "listeBanniContainer";
        };
    }

    if (closeListeBanniContainer) {
        closeListeBanniContainer.onclick = function() {
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
            refusadh.style.color = "white";
        };
        
        refusadh.onclick = function() {
            demandeblock.style.display = "none";
            refusblock.style.display = "block";
            refusadh.style.textDecoration = "underline";
            refusadh.style.textDecorationColor = "red";
            refusadh.style.color = "red";
            demandeadh.style.textDecoration = "none";
            demandeadh.style.textDecorationColor = "none";
            demandeadh.style.color = "white";
        };
    }
    
    function showModalBasedOnHash() {
        const hash = window.location.hash;

        if (modContainer) modContainer.style.display = "none";
        if (gestionAdhesionContainer) gestionAdhesionContainer.style.display = "none";
        if (listeAvertiContainer) listeAvertiContainer.style.display = "none"; 
        if (listeBanniContainer) listeBanniContainer.style.display = "none"; 
        
        switch(hash) {
            case "#modContainer":
                if (modContainer) modContainer.style.display = "block";
                break;
            case "#gestionAdhesionContainer":
                if (gestionAdhesionContainer) gestionAdhesionContainer.style.display = "block";
                break;
            case "#listeAvertiContainer": 
                if (listeAvertiContainer) listeAvertiContainer.style.display = "block";
                break;
            case "#listeBanniContainer": 
                if (listeBanniContainer) listeBanniContainer.style.display = "block";
                break;
            default:
                break;
        }
    }
    
    window.addEventListener("hashchange", showModalBasedOnHash);
    
    showModalBasedOnHash();
});