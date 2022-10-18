// Call the dataTables jQuery plugin

$(document).ready(function() {
    $('#dataTable').DataTable();
    // let displayTable = $('table.display-table').DataTable({
    //     dom: 'Bfrtip',
    //     "order": [[3, "DESC" ]],
    //     buttons: [
    //     {
    //         extend: 'pdf',
    //         split: [
    //         'excel',
    //         'csv',
    //         'copy',
    //         'print',
    //         ],
    //     },
    //     'colvis',
    //     ],
    //     language: {
    //         buttons: {
    //             colvis: 'Visible'
    //         }
    //     },
    //     "search": {
    //         "search": ""
    //     }
    // });

    // let tableHeader = $("#DataTables_Table_0_filter");
    // tableHeader.addClass("d-flex justify-content-between py-2");
    // displayTable.buttons().container().prependTo('#DataTables_Table_0_filter');

    // let table = $('table.sm-dataTables').DataTable();

    // let table = $('table.sm-dataTables').DataTable({
    //     dom: 'Bfrtip',
    //     buttons: [
    //     'copy',
    //     'csv',
    //     ]
    // });


} );