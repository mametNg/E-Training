<?php if ($this->allowFile): ?>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white shadow">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="<?= $this->base_url("admin/logout") ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="air-badge position-fixed zindex-102"></div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= $this->base_url() ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $this->base_url() ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= $this->base_url() ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $this->base_url() ?>/assets/vendor/jsencrypt/dist/jsencrypt.js"></script>

    <!-- Page level plugins -->
    <script src="<?= $this->base_url() ?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?= $this->base_url() ?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="<?= $this->base_url() ?>/assets/vendor/chart.js/dist/chart.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= $this->base_url() ?>/assets/js/features/chart.js"></script>
    <script src="<?= $this->base_url() ?>/assets/vendor/ckeditor/ckeditor.js"></script>
    <script src="<?= $this->base_url() ?>/assets/js/addons/dataTables.js"></script>
    <script src="<?= $this->base_url() ?>/assets/js/sb-admin-2.min.js"></script>
    <script src="<?= $this->base_url() ?>/assets/js/addons/chart-area.js"></script>
    <script src="<?= $this->base_url() ?>/assets/js/features/function.js"></script>
    <script src="<?= $this->base_url() ?>/assets/js/features/admin.js"></script>

</body>

</html>
<?php endif; ?>