window.onload = confirmDelete;

function confirmDelete(){
    let deleteButton = document.getElementById('delete');
    let deleteLink = document.getElementById('deleteLink')
    deleteButton.addEventListener('click', function(){
        if(!confirm('Êtes-vous certain de vouloir supprimer cette sortie ?')) {
            deleteLink.href = location;
        }
    });
}