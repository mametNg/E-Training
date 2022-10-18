<?php if ($this->allowFile): ?>
    
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= $this->base_url('admin') ?>">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-code"></i>
                </div>
                <div class="sidebar-brand-text mx-3">E-TRAINING</div>
            </a>

            <?php foreach ($data['menu'] as $menu) : ?>

            <?php if ($menu['status'] == "1") : ?>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="<?= $this->base_url() ?>/admin<?= $this->e($menu['url']) ?>">
                    <i class="<?= $menu['icon'] ?>"></i>
                    <span><?= ucwords($this->e($menu['name'])) ?></span>
                </a>
            </li>
            <?php endif; ?>
            <?php endforeach; ?>

            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

<?php endif; ?>