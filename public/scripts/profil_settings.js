document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("paramContainer");
    const button = document.getElementById("btnParametres");
    const close_button = document.getElementById("closeContainer");
    
    button.onclick = function(){
        window.location.hash = "paramContainer";
    }
    
    close_button.onclick = function(){
        // Effacer le hash de l'URL sans recharger la page
        history.pushState("", document.title, window.location.pathname + window.location.search);
        // On l'appel car le changement de hash ne déclenche pas l'événement hashchange
        showModalBasedOnHash();
    }
    
    const modaux = [
        document.getElementById("modalPseudo"),
        document.getElementById("modalEmail"),
        document.getElementById("modalBio"),
        document.getElementById("modalMdp")
    ];
    
    const buttons = [
        document.getElementById("btnPseudo"),
        document.getElementById("btnEmail"),
        document.getElementById("btnBio"),
        document.getElementById("btnMdp")
    ];
    
    const close_buttons = [
        document.getElementById("closePseudo"),
        document.getElementById("closeEmail"),
        document.getElementById("closeBio"),
        document.getElementById("closeMdp")
    ];
    
    for(let i = 0; i < modaux.length; i++){
        buttons[i].onclick = function(){
            window.location.hash = modaux[i].id;
        }
        close_buttons[i].onclick = function(){
            window.location.hash = "paramContainer";
        }
    }
    
    function showModalBasedOnHash() {
        const hash = window.location.hash;
        
        // Cacher tous les modaux d'abord
        modal.style.display = "none";
        for(let i = 0; i < modaux.length; i++) {
            modaux[i].style.display = "none";
        }
        
        switch(hash) {
            case "#paramContainer":
                modal.style.display = "block";
                clearSessionErrors();
                break;
            case "#modalPseudo":
                modaux[0].style.display = "block";
                break;
            case "#modalEmail":
                modaux[1].style.display = "block";
                break;
            case "#modalBio":
                modaux[2].style.display = "block";
                break;
            case "#modalMdp":
                modaux[3].style.display = "block";
                break;
            default:
                // Aucun modal à afficher
                break;
        }
    }

    // Écouteur pour les changements de hash dans l'URL
    window.addEventListener("hashchange", showModalBasedOnHash);
    
    // Exécuter immédiatement pour traiter le hash initial de l'URL
    showModalBasedOnHash();
});