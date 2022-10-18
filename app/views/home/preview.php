<?php if ($this->allowFile): ?>
	<!-- Content Row -->
	<div class="row main-js" my-js="preview">

		<div class="col-lg-3">
			<div class="card shadow mb-4">
				<div class="card-header py-3 d-flex justify-content-between">
					<h6 class="mb-0 mt-2 font-weight-bold text-primary">Result Preview</h6>
				</div>
				<div class="card-body">
					<div class="row no-gutters mb-2">
						<div class="col-md-4">
							<img src="<?= $this->base_url("assets/img/account/default.jpg") ?>" class="img-thumbnail">
						</div>
						<div class="col-md-8">
							<div class="card-body">
								<h5 class="card-title"><?= $data['user']['name'] ?></h5>
								<table class="card-text">
									<tr>
										<td>BU</td>
										<td>:</td>
										<td><?= $_SESSION['quest']['bu'] ?></td>
									</tr>
									<tr>
										<td>Area</td>
										<td>:</td>
										<td><?= $_SESSION['quest']['area'] ?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					
					<div class="form-row">
						<div class="form-group col-md-6 mb-lg-0">
							<button class="btn btn-primary btn-block btn-small" id="btn-home">Home</button>
						</div>
						<div class="form-group col-md-6 mb-0">
							<button class="btn btn-success btn-block btn-small" id="btn-try">Try</button>
						</div>						
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-9">
			<div class="card shadow mb-4">
				<div class="card-header py-3 d-flex justify-content-between">
					<h6 class="mb-0 mt-2 font-weight-bold text-primary">Result Preview</h6>
				</div>
				<div class="card-body">
					<?php foreach ($data['results'] as $result) : ?>
						<div class="mb-2">
							<p class="small mb-0">Category</p>
							<h4 class="card-text font-weight-bold"><?= $this->e($result['cat']) ?></h4>
							<p class="mt-0"><?= $this->e($result['score']) ?> : <?= $this->e($result['desc']) ?></p>
						</div>
						<hr class="mt-0">
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Delete exam -->
	<div class="modal modal-secondary fade" id="modal-try-quest" tabindex="-1" role="dialog" aria-labelledby="modal-try" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="text-center">
						<div class="icon text-warning">
							<i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
						</div>
						<h5 class="mt-4">Are you sure you want to Try!</h5>
						<p class="text-sm text-sm">All answers that have been tryed cannot be changed.</p>
					</div>
					<div class="d-flex justify-content-center">
						<div class="m-2">
							<button type="button" id="btn-try-quest" data-info="" data-role class="btn btn-warning">Try Now</button>
						</div>
						<div class="m-2">
							<button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Home-->
	<div class="modal modal-secondary fade" id="modal-back-home" tabindex="-1" role="dialog" aria-labelledby="modal-home" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="text-center">
						<div class="icon text-warning">
							<i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
						</div>
						<h5 class="mt-4">Are you sure you want to back to Home!</h5>
						<p class="text-sm text-sm">All answers will be saved.</p>
					</div>
					<div class="d-flex justify-content-center">
						<div class="m-2">
							<button type="button" id="btn-back-home" data-info="" data-role class="btn btn-warning">Home</button>
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


