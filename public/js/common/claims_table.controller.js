var claimsTable = null;
var columnsTable = null;
var targets = null;
$(document).ready(function () {

    if (numberImpresion == "administrator") {
        columnsTable = [
            {"data": "vin"},
            {"data": "model"},
            {"data": "carrier"},
            {"data": "arrive_date"},
            {"data": "dealer"},
            {"data": "options"}];
        targets = 5;
    } else {
        columnsTable = [
            {"data": "vin"},
            {"data": "model"},
            {"data": "carrier"},
            {"data": "arrive_date"},
            {"data": "options"}];
        targets = 4;
    }
    claimsTable = $('#claims').DataTable({
        "processing": true,
        "serverSide": true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'copy'},
            {extend: 'csv'},
            {extend: 'excel', title: 'ExampleFile'},
            {extend: 'pdf', title: 'ExampleFile'},

            {extend: 'print',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');

                    $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                }
            }
        ],
        "ajax": {
            "url": path + "/allclaims",
            "dataType": "json",
            "type": "POST",
            "data": {_token: $('[name="_token"]').val(), code_table: $('[name="code_table"]').val(), status_table: $('[name="status_table"]').val()}

        }, 'columnDefs': [
            {
                "targets": targets,
                "className": "text-center",
                "width": "20%"
            }],
        "columns": columnsTable

    });
});
