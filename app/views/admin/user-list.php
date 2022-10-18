<?php if ($this->allowFile): ?>
	<div class="row main-js" my-js="user-list">
		<div class="col-lg-12">
			<div class="card shadow">
				<div class="card-header py-3 d-flex justify-content-between" >
					<h6 class="mb-0 mt-2 font-weight-bold text-primary">User List</h6>
					<button class="btn btn-primary btn-small" data-toggle="modal" data-target="#modal-new-user">Add new user</button>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
							<thead>
								<tr>
									<th>No</th>
									<th>Username</th>
									<th>Name</th>
									<th>Passwd</th>
									<th>Dept</th>
									<th>Phone</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>No</th>
									<th>Username</th>
									<th>Name</th>
									<th>Passwd</th>
									<th>Dept</th>
									<th>Phone</th>
									<th>Status</th>
									<th>Action</th>
								</tr>               
							</tfoot>
							<tbody>
								<?php $i=1; foreach ($data['users'] as $user) : ?>
								<tr>
									<td class="text-wrap align-middle text-center"><?= $i ?></td>
									<td class="text-wrap align-middle text-center"><?= $this->e($user['id']) ?></td>
									<td class="text-wrap align-middle text-center"><?= $this->e($user['name']) ?></td>
									<td class="text-wrap align-middle text-center"><?= $this->e($user['password']) ?></td>
									<td class="text-wrap align-middle text-center"><?= $this->e($user['dept']) ?></td>
									<td class="text-wrap align-middle text-center"><?= $this->e($user['phone']) ?></td>
									<td class="text-wrap align-middle text-center"><?= (strtolower($this->e($user['status'])) ? "Active":"Non-Active") ?></td>
									<td class="text-wrap align-middle text-center">
										<div class="d-flex justify-content-between">
											<button type="button" id="btn-modal-edit-user" target="<?= $this->e($user['id']) ?>" class="btn btn-success btn-sm m-1 badge">
												<span class="mr-2">Edit</span><i class="fas fa-fw fa-edit"></i>
											</butto>
											<button type="button" id="btn-modal-delete-user" target="<?= $this->e($user['id']) ?>" class="btn btn-danger btn-sm m-1 badge">
												<span class="mr-2">Delete</span><i class="fas fa-fw fa-trash"></i>
											</butto>
											<?php if (strtolower($this->e($user['status']))): ?>
												<button type="button" id="btn-modal-disable-user" target="<?= $this->e($user['id']) ?>" class="btn btn-dark btn-sm m-1 badge">
													<span class="mr-2">Disable</span><i class="fas fa-fw fa-eye"></i>
												</butto>
											<?php elseif (!strtolower($this->e($user['status']))): ?>
												<button type="button" id="btn-modal-enable-user" target="<?= $this->e($user['id']) ?>" class="btn btn-secondary btn-sm m-1 badge">
													<span class="mr-2">Enable</span><i class="fas fa-fw fa-eye"></i>
												</butto>
											<?php endif; ?>
										</div>
									</td>
								</tr>
								<?php $i++; endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>


	<!-- Modal New user-->
	<div class="modal fade" id="modal-new-user" tabindex="-1" aria-labelledby="modal-new-user" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add new user</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<div class="form-group">
						<label for="input-new-username">Username</label>
						<input type="text" name="input-new-username" id="input-new-username" class="form-control" placeholder="Username" required>
						<div class="invalid-feedback" id="msg-input-new-username"></div>
					</div>

					<div class="form-group">
						<label for="input-new-name">Name</label>
						<input type="text" name="input-new-name" id="input-new-name" class="form-control" placeholder="Name" required>
						<div class="invalid-feedback" id="msg-input-new-name"></div>
					</div>

					<div class="form-group">
						<label for="input-new-password">Password</label>
						<input type="password" name="input-new-password" id="input-new-password" class="form-control" placeholder="Password" required>
						<div class="invalid-feedback" id="msg-input-new-password"></div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input-new-departement">Departement</label>
							<input type="text" name="input-new-departement" id="input-new-departement" class="form-control" placeholder="Departement" required>
							<div class="invalid-feedback" id="msg-input-new-departement"></div>
						</div>

						<div class="form-group col-md-6">
							<label for="input-new-phone">Phone</label>
							<input type="text" name="input-new-phone" id="input-new-phone" class="form-control" placeholder="Phone" required>
							<div class="invalid-feedback" id="msg-input-new-phone"></div>
						</div>
					</div>

					<div class="form-group">
						<label for="input-new-address">Address</label>
						<input type="text" name="input-new-address" id="input-new-address" class="form-control" placeholder="Address" required>
						<div class="invalid-feedback" id="msg-input-new-address"></div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="btn-add-new-user">Save changes</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal edit user-->
	<div class="modal fade" id="modal-edit-user" target="" tabindex="-1" aria-labelledby="modal-edit-user" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit user</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<div class="form-group">
						<label for="input-edit-username">Username</label>
						<input type="text" name="input-edit-username" id="input-edit-username" class="form-control" placeholder="Username" disabled>
						<div class="invalid-feedback" id="msg-input-edit-username"></div>
					</div>

					<div class="form-group">
						<label for="input-edit-name">Name</label>
						<input type="text" name="input-edit-name" id="input-edit-name" class="form-control" placeholder="Name" required>
						<div class="invalid-feedback" id="msg-input-edit-name"></div>
					</div>

					<div class="form-group">
						<label for="input-edit-password">Password</label>
						<input type="password" name="input-edit-password" id="input-edit-password" class="form-control" placeholder="Password" required>
						<div class="invalid-feedback" id="msg-input-edit-password"></div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input-edit-departement">Departement</label>
							<input type="text" name="input-edit-departement" id="input-edit-departement" class="form-control" placeholder="Departement" required>
							<div class="invalid-feedback" id="msg-input-edit-departement"></div>
						</div>

						<div class="form-group col-md-6">
							<label for="input-edit-phone">Phone</label>
							<input type="text" name="input-edit-phone" id="input-edit-phone" class="form-control" placeholder="Phone" required>
							<div class="invalid-feedback" id="msg-input-edit-phone"></div>
						</div>
					</div>

					<div class="form-group">
						<label for="input-edit-address">Address</label>
						<input type="text" name="input-edit-address" id="input-edit-address" class="form-control" placeholder="Address" required>
						<div class="invalid-feedback" id="msg-input-edit-address"></div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="btn-save-edit-user">Save changes</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Disable user -->
	<div class="modal modal-secondary fade" id="modal-disable-user" tabindex="-1" role="dialog" aria-labelledby="modal-disable-user" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="text-center">
						<div class="icon text-seconfary">
							<i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
						</div>
						<h5 class="mt-4">Are you sure you want to disable it now!</h5>
						<p class="text-sm text-sm">All data User ID <span class="info-disable-user font-weight-bolder"></span> will be disabled.</p>
					</div>
					<div class="d-flex justify-content-center">
						<div class="m-2">
							<button type="button" id="save-disable-user" data-info="" data-role class="btn btn-seconfary">Disable Now</button>
						</div>
						<div class="m-2">
							<button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Enable user -->
	<div class="modal modal-secondary fade" id="modal-enable-user" tabindex="-1" role="dialog" aria-labelledby="modal-enable-user" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="text-center">
						<div class="icon text-seconfary">
							<i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
						</div>
						<h5 class="mt-4">Are you sure you want to enable it now!</h5>
						<p class="text-sm text-sm">All data user ID <span class="info-enable-user font-weight-bolder"></span> will be enabled.</p>
					</div>
					<div class="d-flex justify-content-center">
						<div class="m-2">
							<button type="button" id="save-enable-user" data-info="" data-role class="btn btn-seconfary">Enable Now</button>
						</div>
						<div class="m-2">
							<button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Delete user -->
	<div class="modal modal-secondary fade" id="modal-delete-user" tabindex="-1" role="dialog" aria-labelledby="modal-delete-user" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="text-center">
						<div class="icon text-danger">
							<i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
						</div>
						<h5 class="mt-4">Are you sure you want to delete it now!</h5>
						<p class="text-sm text-sm">All data user ID <span class="info-delete-user font-weight-bolder"></span> will be deleted.</p>
					</div>
					<div class="d-flex justify-content-center">
						<div class="m-2">
							<button type="button" id="save-delete-user" data-info="" data-role class="btn btn-danger">Delete Now</button>
						</div>
						<div class="m-2">
							<button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>