window.onload = function(){
    reloadLieux();
    prefill();
    //displayButtonDelete();
}

function reloadLieux(){

    // Récupération des selects ville et lieu
    let sortie_form_ville = document.getElementById('sortie_form_ville');
    let sortie_form_lieu = document.getElementById('sortie_form_lieu');

    //Ajout d'un event listener sur le select "ville"
    sortie_form_ville.addEventListener('change', function(){

        let ville = sortie_form_ville.value;
        let data = {'ville' : ville};
        let lieux = [];
        let req = new XMLHttpRequest();

        //S'il y a un nombre dans l'url : split de l'url pour retirer ce nombre, puis envoi d'une
        //requete POST vers la route /lieu qui permet de récupérer la liste de lieux
        if(location.href.includes('?bool=1')){
            let url = location.href.replace('?bool=1', '');
            req.open('POST', url + '/lieu');
        } else if (location.href.match(/\d+/)){
            let arrayLocation = location.href.split("/");
            arrayLocation.pop();
            let newLocation = arrayLocation.join("/");
            req.open('POST', newLocation + '/lieu');
        } else {
            req.open('POST', location.href + '/lieu');
        }

        //Décodage des lieux reçus en retour
        req.setRequestHeader("Content-Type", "application/json;charset=utf-8");
        req.onload = function () {
            data = JSON.parse(this.responseText);
            lieux = data['lieux'];

            //Suppression des lieux actuellement dans le select
            for (i = sortie_form_lieu.length - 1; i >= 0; i--) {
                let child = sortie_form_lieu.firstChild;
                child.remove();
            }

            //Ajout des lieux reçus dans le select
            for (let lieu of lieux) {
                let optionLieu = document.createElement('option');
                optionLieu.value = lieu['id'];
                optionLieu.innerText = lieu['nom'];
                sortie_form_lieu.appendChild(optionLieu);

                console.log(optionLieu);
            }
        }
        req.send(JSON.stringify(data));

    })
}


function displayButtonDelete(){

    let matches = location.href.match(/\d+/g);

    if(matches){

        let urlParams = new URLSearchParams(window.location.search);

        console.log(urlParams.has('sortie_id')); // true
        console.log(urlParams.get('sortie_id'));
        let sortie_id = urlParams.get('sortie_id');
        //console.log(urlParams.getAll('action')); // ["edit"]
        //console.log(urlParams.toString()); // "?post=1234&action=edit"
        //console.log(urlParams.append('active', '1')); // "?post=1234&action=edit&active=1"

        let cancelButton = document.getElementById('cancelBtnForAddSortie');
        console.log(cancelButton);

        let supprButton = document.createElement("button");
        supprButton.className = 'btn';
        supprButton.textContent = 'Supprimer la sortie';

        let link = document.createElement("a");
        link.href="http://sortir/sortie/cancel/reason/" + sortie_id;

        supprButton.appendChild(link);
        cancelButton.after(supprButton);

    }
}

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



/*
    $sortie_form_ville.change(function () {
        let $form = $(this).closest('form')

        let data = {}
        data[$token.attr('name')] = $token.val()
        data[$sortie_form_ville.attr('name')] = $sortie_form_ville.val()

        $.post($form.attr('action'), data).then(function (response){
            $('#sortie_form_lieu').replaceWith(
                $(response).find("#sortie_form_lieu")
            )
        })
    })*/

