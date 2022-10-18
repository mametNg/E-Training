<?php if ($this->allowFile): ?>
	<div class="row main-js" my-js="exam-settings">
		<div class="col-lg-12">
			<div class="card shadow">
				<div class="card-header py-3 d-flex justify-content-between" >
					<h6 class="mb-0 mt-2 font-weight-bold text-primary">Pengaturan Ujian</h6>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="input-name-ujian">Nama Ujian</label>
						<input type="text" id="input-name-ujian" class="form-control" value="<?= $this->e($data['regulation']['subject']) ?>">
						<div class="invalid-feedback" id="msg-input-name-ujian"></div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input-processing-time">Processing time</label>
							<input type="number" id="input-processing-time" class="form-control" value="<?= $this->e($data['regulation']['timer']) ?>">
							<div class="invalid-feedback" id="msg-input-processing-time"></div>
						</div>
						<div class="form-group col-md-6">
							<label for="input-minimum-value">Minimum value</label>
							<input type="number" id="input-minimum-value" class="form-control" value="<?= $this->e($data['regulation']['min_val']) ?>">
							<div class="invalid-feedback" id="msg-input-minimum-value"></div>
						</div>
					</div>
					<div class="form-group">
						<label for="input-regulation">Regulation</label>
						<textarea name="regulation" class="form-control" id="input-regulation" rows="10" cols="80" placeholder="regulation" required><?= $this->e(base64_decode($data['regulation']['rule'])) ?></textarea>
						<div class="invalid-feedback" id="msg-input-regulation"></div>
					</div>
					<div class="form-group">
						<button type="button" class="btn btn-primary" id="btn-save">Save</button>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>