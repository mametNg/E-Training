<?php if ($this->allowFile): ?>
    <?php if (isset($this->allowFile) && $this->allowFile): ?>

        <!-- Outer Row -->
        <div class="row justify-content-center mt-5 main-js" my-js="admin">

            <div class="col-xl-7 col-lg-6 col-md-9 mt-5">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome <?= $this->e($data['header']['brand']) ?>!</h1>
                                    </div>
                                    <form class="user">
                                       <div class="form-group">
                                            <input type="text" id="input-uname" class="form-control form-control-user" aria-describedby="emailHelp" value="<?= $this->e($data['user']) ?>" placeholder="Username...">
                                            <div id="msg-input-uname" class="invalid-feedback"></div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span type="button" class="input-group-text rounded-pill-left" id="turn-passwd">
                                                    <i class="fa fa-fw fa-eye-slash"></i>
                                                </span>
                                            </div>
                                            <input type="password" id="input-password" class="form-control rounded-pill-right form-control-user" placeholder="Password" aria-label="Username" aria-describedby="basic-addon1">
                                            <div id="msg-input-password" class="invalid-feedback"></div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <button type="button" id="btn-login" class="btn btn-primary btn-user btn-block">Login</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
<?php endif; ?>
<?php endif; ?>