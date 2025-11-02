$(function () {
            $.extend(true, $.fn.dataTable.defaults, {
                "dom": '<"top">t<"bottom"ilrp<"clear">>',
                "sortCellsTop": true,
                "sort": false,
                "info": false,
                "paging": false,
                "lengthChange": false,
                "language": {
                    "sProcessing": '<div class="preloader-wrapper small active"> <div class="spinner-layer"> <div class="circle-clipper left"> <div class="circle"></div> </div><div class="gap-patch"> <div class="circle"></div> </div><div class="circle-clipper right"> <div class="circle"></div> </div> </div> </div>',
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "<i class='material-icons'>first_page</i>",
                        "sLast": "<i class='material-icons'>last_page</i>",
                        "sNext": "<i class='material-icons'>navigate_next</i>",
                        "sPrevious": "<i class='material-icons'>navigate_before</i>"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                }
            });
        });
