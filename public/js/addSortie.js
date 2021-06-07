window.onload = function(){
    reloadLieux();
    displayButtonAjoutLieu();
}

function reloadLieux(){

    let sortie_form_ville = document.getElementById('sortie_form_ville');
    console.log(sortie_form_ville);
    let sortie_form_lieu = document.getElementById('sortie_form_lieu');
    console.log(sortie_form_lieu);

    sortie_form_ville.addEventListener('change', function(){

        let ville = sortie_form_ville.value;
        console.log(ville);
        let data = {'ville' : ville};
        let lieux = [];
        let req = new XMLHttpRequest();
        req.open('POST', location.href + '/lieu');
        req.setRequestHeader("Content-Type", "application/json;charset=utf-8");
        req.onload = function () {
            data = JSON.parse(this.responseText);
            lieux = data['lieux'];

            console.log(lieux);

            for (i = sortie_form_lieu.length - 1; i >= 0; i--) {
                let child = sortie_form_lieu.firstChild;
                child.remove();
            }

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



function displayButtonAjoutLieu(){

    if(document.getElementById('sortie_form_lieu')){

        let lieux = document.getElementById('sortie_form_lieu');
        console.log(lieux);

        let submitButton = document.getElementById('submitBtnForNewSortie');
        console.log(submitButton);

        let lieuButton = document.createElement("button");
        lieuButton.className = 'btn';
        lieuButton.textContent = 'Ajouter un lieu';

        let link = document.createElement("a");
        link.href="{{ path('sortie_lieu') }}";

        lieuButton.appendChild(link);
        submitButton.after(lieuButton);
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

