window.onload = displayReason;

function displayReason() {
    let etat = document.getElementById('etat');
    let parent = document.getElementById('parent');
    etat.addEventListener('mouseover', function () {
        let divRow = document.createElement('div');
        divRow.className = 'row';
        divRow.id = 'ajax';
        let divCol = document.createElement('div');
        divCol.className = 'col-lg-4 offset-lg-7';
        let motif = document.createElement('strong');
        getMotif(motif);
        divCol.appendChild(motif);
        divRow.appendChild(divCol);
        parent.appendChild(divRow);
    })
    etat.addEventListener('mouseleave', function () {
        let toDelete = document.getElementById('ajax');
        toDelete.remove();
    })
}

function getMotif(element) {
    let sortie_id = document.getElementById('sortie_id').value;
    let data = {sortie_id: sortie_id};
    let req = new XMLHttpRequest();
    req.open('POST', location.href + '/ajax/motif');
    req.setRequestHeader("Content-Type", "application/json;charset=utf-8");
    req.onload = function () {
        data = JSON.parse(this.responseText);
        element.innerText = data['motif'];
    }
    req.send(JSON.stringify(data));
}