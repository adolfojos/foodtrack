<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="theme-color" content="#ffffff" />
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png" />
    <title>FOODTRACK - Login</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>vendors/material-icons/material-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>vendors/materialize-src/sass/materialize.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>vendors/materialize-datatables/css/dataTables.materialize.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/link-options.css">

    <style>
        main { flex: 1 0 auto; }
        .container { max-width: 600px; }
        .btn { padding: 0 2rem; font-size: 15px; }
    </style>
</head>
<body>

    <div style="text-align: center;">
        <a href="">
            <h1 class="logo-p"></h1>
        </a>

        <div class="container">
            <div class="z-depth-1 white row">
                <div class="clap col s12" style="height: 10px;"></div>

                <div class="row row-end">
                    <div class="col s12">
                        <form method="post" action="<?= BASE_URL ?>auth/process" id="login-form" class="col s12" autocomplete="off">
                            <div style="padding: 30px;">
                                <!-- Campo User -->
                                <div class="input-field col s12 m6 offset-m3">
                                    <input type="text" id="username" name="username" required autocomplete="off" class="uppercase identification_letter_fix" />
                                    <label class="required" for="email">Username</label>
                                </div>

                                <!-- Campo Password -->
                                <div class="input-field col s12 m6 offset-m3">
                                    <input type="password" id="password" name="password" required autocomplete="off" />
                                    <label class="required" for="password">Password</label>

                                    <!-- Mensajes Flash -->
                                    <?php if (isset($_SESSION['flash_message'])): ?>
                                        <?php $flash = $_SESSION['flash_message']; ?>
                                        <div class="error flash-<?= htmlspecialchars($flash['type']) ?>">
                                            <?= htmlspecialchars($flash['message']) ?>
                                        </div>
                                        <?php unset($_SESSION['flash_message']); ?>
                                    <?php endif; ?>

                                    <?php if (isset($error)): ?>
                                        <div class="error left-align">
                                            <div><?= htmlspecialchars($error) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Botón de envío -->
                                <div class="row row-end">
                                    <div class="btn-enter input-field col s12 m6 offset-m3">
                                        <button type="submit" class="col s12 btn btn-medium waves-effect waves-light" accesskey="l" id="submit" tabindex="6">
                                            Log in
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Enlace de recuperación -->
            <div class="col s12 form-footer" style="margin-bottom: 10px;">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="<?= BASE_URL ?>forgot-password">
                    Forgot your password? 
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= BASE_URL ?>vendors/jquery/jquery.min.js"></script>
    <script src="<?= BASE_URL ?>vendors/materialize/dist/js/materialize.min.js"></script>
    <script src="<?= BASE_URL ?>vendors/datatables/dist/js/jquery.dataTables.min.js"></script>
    <script src="<?= BASE_URL ?>vendors/jquery-mask/dist/jquery.mask.min.js"></script>
    <script src="<?= BASE_URL ?>vendors/jquery-maskMoney/dist/jquery.maskMoney.min.js"></script>
    <script src="<?= BASE_URL ?>vendors/materialize-datatables/js/dataTables.materialize.js"></script>
    <script src="<?= BASE_URL ?>js/main.js"></script>
    <script src="<?= BASE_URL ?>js/session-management.js"></script>
    <script src="<?= BASE_URL ?>js/custom-toast.js"></script>
    <script src="<?= BASE_URL ?>js/inactivity-logout.js"></script>

    <!-- Configuración de DataTables -->
    <script>
        $(function () {
            $.extend(true, $.fn.dataTable.defaults, {
                dom: '<"top">t<"bottom"ilrp<"clear">>',
                sortCellsTop: true,
                sort: false,
                info: false,
                paging: false,
                lengthChange: false,
                language: {
                    sProcessing: '<div class="preloader-wrapper small active"><div class="spinner-layer"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div>',
                    sLengthMenu: "Show _MENU_ records",
                    sZeroRecords: "No results found",
                    sEmptyTable: "No data available in this table",
                    sInfo: "Showing records from _START_ to _END_ out of _TOTAL_ total",
                    sInfoEmpty: "Showing records from 0 to 0 out of 0 total",
                    sInfoFiltered: "(filtered from _MAX_ total records)",
                    sSearch: "Search:",
                    oPaginate: {
                        sFirst: "<i class='material-icons'>first_page</i>",
                        sLast: "<i class='material-icons'>last_page</i>",
                        sNext: "<i class='material-icons'>navigate_next</i>",
                        sPrevious: "<i class='material-icons'>navigate_before</i>"
                    },
                    oAria: {
                        sSortAscending: ": Activate to sort column ascending",
                        sSortDescending: ": Activate to sort column descending"
                    }
                }
            });
        });
    </script>

    <!-- Inicialización de componentes -->
    <script>
        $(document).ready(function () {
            $('.button-collapse').sideNav();
            $('.modal').modal();
        });
    </script>

    <!-- Modal de inactividad -->
    <div id="message-alert-logout" class="modal">
        <div class="modal-content">
            <p>No activity detected in the last 8 minutes.</p>
            <p>If no activity is detected in the SGEN Platform within the next 2 minutes, you will be redirected to the homepage.</p>
        </div>
        <div class="modal-footer">
            <a id="alert-accept" href="#!" class="modal-action modal-close waves-effect btn">Accept</a>
        </div>
    </div>
</body>
</html>
