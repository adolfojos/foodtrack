            </div>
        <div class="clearfix"></div>
    </main>
    <!-- Footer -->
    <footer class="center-align">
        <span><?= date('Y') ?> © foodtrack - Food tracking system.</span>
    </footer>
    <!-- Scripts -->
    <script src="<?= VENDOR_PATH ?>jquery/jquery.min.js"></script>
    <script src="<?= VENDOR_PATH ?>materialize/dist/js/materialize.min.js"></script>
    <script src="<?= VENDOR_PATH ?>datatables/dist/js/jquery.dataTables.min.js"></script>
    <script src="<?= VENDOR_PATH ?>jquery-mask/dist/jquery.mask.min.js"></script>
    <script src="<?= VENDOR_PATH ?>jquery-maskMoney/dist/jquery.maskMoney.min.js"></script>
    <script src="<?= VENDOR_PATH ?>materialize-datatables/js/dataTables.materialize.js"></script>
    <script src="<?= JS_PATH ?>main.js"></script>
    <script src="<?= JS_PATH ?>session-management.js"></script>
    <script src="<?= JS_PATH ?>custom-toast.js"></script>
    <script src="<?= JS_PATH ?>datatables-global.js"></script>
    <script src="<?= JS_PATH ?>inactivity-logout.js"></script>
    <script src="<?= JS_PATH ?>identification-letter-fix.js"></script>
    <!-- Inicialización de componentes -->
    <script>
        $(document).ready(function() {
            $('.button-collapse').sideNav();
            $('.modal').modal();
        });
        // Ocultar toast después de 5 segundos
        setTimeout(() => {
            const toast = document.querySelector('#toast-container .toast');
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }
        }, 5000);
    </script>
    <!-- Toast -->
    <div id="toast-container">
        <div class="toast green" style="top: 0px; opacity: 1; flex: 1;">
            <i class="material-icons">check_circle</i>
            <span class="toast-text"></span>
        </div>
    </div>
    <!-- Modal de inactividad -->
    <div id="message-alert-logout" class="modal">
        <div class="modal-content">
            <p>No activity detected in the last 8 minutes.</p>
            <p>If no activity is detected in the foodtrack Platform within the next 2 minutes, you will be redirected to the homepage.</p>
        </div>
        <div class="modal-footer">
            <a id="alert-accept" href="#!" class="modal-action modal-close waves-effect btn">Accept</a>
        </div>
    </div>
    <!-- Máscara DNI -->
    <script src="<?= JS_PATH ?>dni-masc.js"></script>
</body>
</html>
