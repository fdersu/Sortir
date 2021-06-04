window.onload = displayButtonAjoutLieu;

function reloadLieux(){

    let $sortie_form_ville = document.getElementById('sortie_form_ville');
    let $token = document.getElementById('sortie_token')

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


