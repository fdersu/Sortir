window.onload = displayReason;

function displayReason() {
    let etat = document.getElementsByClassName('etat');
    for(let item of etat){
        item.addEventListener('click', function () {
            let sortieId = item.parentElement.lastElementChild.value;
            console.log(sortieId);
            let parent = getParentDiv(item, 7);
            console.log(parent);
            let divRow = document.createElement('div');
            divRow.className = 'row';
            divRow.id = 'ajax';
            let divCol = document.createElement('div');
            divCol.className = 'col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 col-xxl-8 ' +
                                'offset-0 offset-sm-4 offset-md-4 offset-lg-2 offset-xl-2 offset-xxl-2';
            divCol.id = 'ajaxDisplay';
            let motif = document.createElement('strong');
            getMotif(motif, sortieId);
            divCol.appendChild(motif);
            divRow.appendChild(divCol);
            parent.appendChild(divRow);
        })
        item.addEventListener('mouseenter', function () {
            if(document.getElementById('ajax') != null) {
                let toDelete = document.getElementById('ajax');
                toDelete.remove();
            }
        })
    }
}

function getMotif(element, sortieId) {
    let data = {id: sortieId};
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