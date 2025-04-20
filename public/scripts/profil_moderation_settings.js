document.addEventListener('DOMContentLoaded', function() {    
    const modContainer = document.getElementById("profilmodContainer");
    const btnModeration = document.getElementById("btnModeration");
    const closeModContainer = document.getElementById("closeModContainer");
    
    const modalWarn = document.getElementById("modalWarn");
    const modalBan = document.getElementById("modalBan");
    const modalCancel = document.getElementById("modalCancel");
    const modalSuppr = document.getElementById("modalSuppr");

    const btnWarn = document.getElementById("btnwarn");
    const btnBan = document.getElementById("btnban");
    const btnCancel = document.getElementById("btncancel");
    const btnDelete = document.getElementById("btndelete");
    
    const closeWarn = document.getElementById("closeWarn");
    const closeBan = document.getElementById("closeBan");
    const closeCancel = document.getElementById("closeCancel");
    const closeSuppr = document.getElementById("closeSuppr");
    
    if (btnModeration) {
        btnModeration.onclick = function() {
            window.location.hash = "profilmodContainer";
        };
    }
    
    if (closeModContainer) {
        closeModContainer.onclick = function() {
            history.pushState("", document.title, window.location.pathname + window.location.search);
            showModalBasedOnHash();
        };
    }
    
    if (btnWarn) {
        btnWarn.addEventListener('click', function() {
            window.location.hash = "modalWarn";
        });
    }
    
    if (btnBan) {
        btnBan.addEventListener('click', function() {
            window.location.hash = "modalBan";
        });
    }
    
    if (btnCancel) {
        btnCancel.addEventListener('click', function() {
            window.location.hash = "modalCancel";
        });
    }
    
    if (btnDelete) {
        btnDelete.addEventListener('click', function() {
            window.location.hash = "modalSuppr";
        });
    }
    
    if (closeWarn) {
        closeWarn.onclick = function() {
            window.location.hash = "profilmodContainer";
        };
    }
    
    if (closeBan) {
        closeBan.onclick = function() {
            window.location.hash = "profilmodContainer";
        };
    }
    
    if (closeCancel) {
        closeCancel.onclick = function() {
            window.location.hash = "profilmodContainer";
        };
    }
    
    if (closeSuppr) {
        closeSuppr.onclick = function() {
            window.location.hash = "profilmodContainer";
        };
    }
    
    function showModalBasedOnHash() {
        const hash = window.location.hash;

        if (modContainer) modContainer.style.display = "none";
        if (modalWarn) modalWarn.style.display = "none";
        if (modalBan) modalBan.style.display = "none";
        if (modalCancel) modalCancel.style.display = "none";
        if (modalSuppr) modalSuppr.style.display = "none";
        
        switch(hash) {
            case "#profilmodContainer":
                if (modContainer) {
                    modContainer.style.display = "block";
                }
                break;
            case "#modalWarn":
                if (modalWarn) {
                    modalWarn.style.display = "block";
                }
                break;
            case "#modalBan":
                if (modalBan) {
                    modalBan.style.display = "block";
                }
                break;
            case "#modalCancel":
                if (modalCancel) {
                    modalCancel.style.display = "block";
                }
                break;
            case "#modalSuppr":
                if (modalSuppr) {
                    modalSuppr.style.display = "block";
                }
                break;
            default:
                break;
        }
    }
    
    window.addEventListener("hashchange", function() {
        showModalBasedOnHash();
    });
    
    showModalBasedOnHash();
});