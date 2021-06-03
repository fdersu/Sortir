let $sortie_form_ville = $('#sortie_form_ville')
let $token = $('#sortie_token')

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