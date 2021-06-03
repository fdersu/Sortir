window.onload = confirmCancel;

function confirmCancel(){
    let cancelButton = document.getElementById('cancel');
    let cancelLink = document.getElementById('cancelLink')
    cancelButton.addEventListener('click', function(){
        if(!confirm('Êtes-vous certain de vouloir annuler cette sortie ?')) {
            cancelLink.href = location;
        }
    });
}

