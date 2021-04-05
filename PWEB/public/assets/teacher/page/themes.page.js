var tableClasses = $("#themes-table").DataTable({
    "sPaginationType": "full_numbers",
    aoColumnDefs: [
        {
            bSortable: false,
            aTargets: [-1]
        }
    ]
});

function del(url){
    bootbox.confirm({
        message: "Voulez-vous vraiment supprimer ce thème ? <br> Cette action est irréversible. ",
        buttons: {
            confirm: {
                label: 'Oui',
                className: 'btn-danger'
            },
            cancel: {
                label: 'Non',
                className: 'btn-info'
            }
        },
        callback: function (result) {
            if(result){
                remove(url);
            }
        }
    });
}

function remove(url){
    $.ajax({
        url: url,
        type: "get",
        dataType: "json",
        success: function(json, statut){
            if(!json.result.error){
                bootbox.alert("<center><i class=\"fa fa-check-circle fa-5\" style=\"color: green; font-size: 5em;margin-left: 10px;margin-bottom: 10px;\" aria-hidden=\"true\"></i> <p style=\"font-size: 2em;\">" + json.result.success + "</p></center>");
                tableClasses
                    .row($("#theme_" + json.result.id).parent())
                    .remove()
                    .draw();
            }else{
                bootbox.alert("<font color=\"red\">" + json.result.error + "</font>");
            }
        },
        error: function(json, statut, error){
            bootbox.alert("<font color=\"red\">Une erreur est survenue, veuillez réessayer ultérieurement.</font>");
        }
    });
}