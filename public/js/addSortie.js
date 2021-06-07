window.onload = function(){
    reloadLieux();
    //displayButtonDelete();
}

function reloadLieux(){

    let sortie_form_ville = document.getElementById('sortie_form_ville');
    let sortie_form_lieu = document.getElementById('sortie_form_lieu');

    sortie_form_ville.addEventListener('change', function(){

        let ville = sortie_form_ville.value;
        console.log(ville);
        let data = {'ville' : ville};
        let lieux = [];
        let req = new XMLHttpRequest();

        if(location.href.match(/\d+/)){
            let arrayLocation = location.href.split("/");
            arrayLocation.pop();
            let newLocation = arrayLocation.join("/");
            req.open('POST', newLocation + '/lieu');
        } else {
            req.open('POST', location.href + '/lieu');
        }

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

