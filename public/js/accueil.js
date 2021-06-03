window.onload = displayReason;

function displayReason() {
    let etat = document.getElementsByClassName('etat');
    for(let item of etat){
        item.addEventListener('click', function () {
            let sortieDiv = item.parentNode;
            let sortie = sortieDiv.nextSibling;
            let sortie_id = sortie.nextSibling.value;
            console.log(sortie_id);
            let parent = getParentDiv(item, 3);
            console.log(parent);
            let divRow = document.createElement('div');
            divRow.className = 'row';
            divRow.id = 'ajax';
            let divCol = document.createElement('div');
            divCol.className = 'col-lg-4 offset-lg-7';
            let motif = document.createElement('strong');
            getMotif(motif, sortie_id);
            divCol.appendChild(motif);
            divRow.appendChild(divCol);
            parent.appendChild(divRow);
        })
        item.addEventListener('mouseleave', function () {
            if(document.getElementById('ajax') != null) {
                let toDelete = document.getElementById('ajax');
                toDelete.remove();
            }
        })
    }
}

function getMotif(element, sortie_id) {
    let data = {sortie_id: sortie_id};
    let req = new XMLHttpRequest();
    req.open('POST', location.href + '/ajax/motif');
    req.setRequestHeader("Content-Type", "application/json;charset=utf-8");
    req.onload = function () {
        data = JSON.parse(this.responseText);
        element.innerText = data['motif'];
        console.log(element.innerText);
    }
    req.send(JSON.stringify(data));
}

function getParentDiv(element, number){
    let parent = element;
    for(let i = 0; i < number; i++){
        parent = parent.parentNode;
    }
    return parent;
}