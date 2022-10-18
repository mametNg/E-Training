<?php if (isset($this->allowFile) && $this->allowFile): ?>


    </div>

    <div class="air-badge position-fixed zindex-102"></div>
    
    <!-- Bootstrap core JavaScript-->
    <script src="<?= $this->base_url() ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $this->base_url() ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= $this->base_url() ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $this->base_url() ?>/assets/vendor/jsencrypt/dist/jsencrypt.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= $this->base_url() ?>/assets/js/sb-admin-2.min.js"></script>
    <script src="<?= $this->base_url() ?>/assets/js/features/function.js"></script>
    <script src="<?= $this->base_url() ?>/assets/js/features/admin.js"></script>

</body>

</html>
<?php endif; ?>