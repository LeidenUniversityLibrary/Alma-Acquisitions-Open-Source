$(function () {
    //SECTION Datatable
    //Datatable options go here!
    $('#data-table').DataTable({
        responsive: true,
        responsive: {
            details: {
                type: 'column'
            }
        },
        processing: true,
        order: [
            [6, "des"]
        ],
        columnDefs: [
            {
                targets: [6, 7,8],
                className: 'none'
            },
            {
                className: 'dtr-control',
                orderable: false,
                targets:   0
            }

        ],

    });
    $('#data-table-1').DataTable({
        responsive: true,
        processing: true,
        order: [
            [0, "asc"]
        ],
                columnDefs: [
            {
                targets: [3, 4],
                bSortable: false
            },

        ],
    });
});

// Normalize special charachters.
$.fn.DataTable.ext.type.search.string = function (data) {
    return !data ?
        '' :
        typeof data === 'string' ?
        data
        .replace(/[áÁàÀâÂäÄãÃåÅæÆ]/g, 'a')
        .replace(/[çÇ]/g, 'c')
        .replace(/[éÉèÈêÊëË]/g, 'e')
        .replace(/[íÍìÌîÎïÏîĩĨĬĭ]/g, 'i')
        .replace(/[ñÑ]/g, 'n')
        .replace(/[óÓòÒôÔöÖœŒ]/g, 'o')
        .replace(/[ß]/g, 's')
        .replace(/[úÚùÙûÛüÜ]/g, 'u')
        .replace(/[ýÝŷŶŸÿ]/g, 'n') :
        data;
};
