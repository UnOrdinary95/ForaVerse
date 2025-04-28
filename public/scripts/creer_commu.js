document.addEventListener('DOMContentLoaded', function() {
const modal = document.getElementById('creerCommuContainer');
const button = document.getElementById('btnCreerCommu');
const close_button = document.getElementById('closeCommuContainer');

button.onclick = function(){
window.location.hash = "creerCommu";
}

close_button.onclick = function(){
history.pushState("", document.title, window.location.pathname + window.location.search);
showModalBasedOnHash();
}

function showModalBasedOnHash(){
if (window.location.hash === "#creerCommu") {
    modal.style.display = "block";
}
else{
    modal.style.display = "none";
}
}
        

window.addEventListener('hashchange', showModalBasedOnHash);
showModalBasedOnHash();
});
