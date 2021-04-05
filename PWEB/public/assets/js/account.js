var tableClasses = $("#qcm-table").DataTable({
    "sPaginationType": "full_numbers",
    aoColumnDefs: [
        {
            bSortable: false,
            aTargets: [-1]
        }
    ]
});