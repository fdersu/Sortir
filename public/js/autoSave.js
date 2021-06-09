let boutonLieu = document.getElementById('eventTrigger');
console.log(boutonLieu);
let input_nom = document.getElementById('sortie_form_nom');
let input_dateDebut = document.getElementById('sortie_form_dateDebut_date');
let input_dateDebutTime = document.getElementById('sortie_form_dateDebut_time');
let input_duree = document.getElementById('sortie_form_duree');
let input_description = document.getElementById('sortie_form_description');
let input_inscriptions = document.getElementById('sortie_form_nbInscriptionsMax');
let input_dateCloture = document.getElementById('sortie_form_dateCloture');
boutonLieu.addEventListener('mouseover', function(){
    let nom = input_nom.value;
    let dateDebut = input_dateDebut.value;
    let dateDebutTime = input_dateDebutTime.value;
    let duree = input_duree.value;
    let description = input_description.value;
    let inscriptions = input_inscriptions.value;
    let dateCloture = input_dateCloture.value;
    let sortie = {
        'nom': nom,
        'dateDebut': dateDebut,
        'dateDebutTime': dateDebutTime,
        'duree': duree,
        'description': description,
        'inscriptions': inscriptions,
        'dateCloture': dateCloture,
    };
    console.log(sortie);
    localStorage.setItem('sortie', JSON.stringify(sortie));
})
window.onload = prefill;

function prefill(){
    let url = location.href;
    if(url.charAt(url.length - 1) === '1'){
        let sortieJson = localStorage.getItem('sortie');
        let sortie = JSON.parse(sortieJson);
        input_nom.value = sortie.nom;
        input_dateDebut.value = sortie.dateDebut;
        input_dateDebutTime.value = sortie.dateDebutTime;
        input_duree.value = sortie.duree;
        input_description.value = sortie.description;
        input_inscriptions.value = sortie.inscriptions;
        input_dateCloture.value = sortie.dateCloture;
    }
}