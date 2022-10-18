<?php if ($this->allowFile): ?>
	<!-- Content Row -->
	<div class="row justify-content-center main-js" my-js="welcome">
		<div class="col-lg-7">
			<div class="card shadow mb-4">
				<div class="card-body">
					<h2 class="text-center mt-4 mb-4 font-weight-bolder"><?= $this->e($data['regulation']['subject']) ?></h2>
					<div class="py-2">
						<table>
							<tr>
								<td>Time</td>
								<td>:</td>
								<td><?= $this->e($data['regulation']['timer']) ?> Minutes</td>
							</tr>
						</table>
					</div>
					<div>
						<h4 class="font-weight-bolder">Regulation</h4>
						<div class="ml-n3">
							<?= base64_decode($data['regulation']['rule']) ?>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="col-lg-5">
			<div class="card shadow mb-4">
				<div class="card-body">
					<div class="form-group">
						<label for="input-cat-bu">Category</label>
						<select class="form-control" id="input-cat-bu">
							<option disabled selected>Choose a Category</option>
							<?php foreach ($data['soal']['cat'] as $bu) : ?>
								<option value="<?= $this->w3llEncode($this->e($bu['bu'])) ?>"><?= $this->e($bu['bu']) ?></option>
							<?php endforeach; ?>
						</select>
						<div class="invalid-feedback" id="msg-input-cat-bu"></div>
					</div>

					<div class="form-group">
						<label for="input-cat-area">Area</label>
						<select class="form-control" disabled id="input-cat-area">
							<option disabled selected>Choose a Area</option>
						</select>
						<div class="invalid-feedback" id="msg-input-cat-area"></div>
					</div>

					<div class="form-group form-check">
						<input type="checkbox" class="form-check-input" id="input-cat-ready">
						<label class="form-check-label" for="input-cat-ready">I understand and ready to take the test</label>
						<div class="invalid-feedback" id="msg-input-cat-ready"></div>
					</div>
					<button type="submit" class="btn btn-primary" id="btn-start">Start</button>

				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="card shadow mb-4">
				<div class="card-header py-3 d-flex justify-content-between" >
					<h6 class="mb-0 mt-2 font-weight-bold text-primary">History</h6>
				</div>
				<div class="card-body">
					<div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>Score</th>
                  <th>BU</th>
                  <th>Area</th>
                  <th>Category</th>
                  <th>Description</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>Score</th>
                  <th>BU</th>
                  <th>Area</th>
                  <th>Category</th>
                  <th>Date</th>
                  <th>Description</th>
                </tr>               
              </tfoot>
              <tbody>

                <?php $i=1; foreach ($data['historys'] as $history) : ?>
                <tr>
                	<td><?= $i ?></td>
                	<td><?= $this->e($history['name']) ?></td>
                	<td><?= $this->e(substr($history['score'], 0, 3), ".") ?></td>
                	<td><?= $this->e($history['bu']) ?></td>
                	<td><?= $this->e($history['area']) ?></td>
                	<td><?= $this->e($history['cat']) ?></td>
                	<td><?= $this->e($history['desc']) ?></td>
                	<td><?= date("d-m-Y h:i:s a", $this->e($history['created'])) ?></td>
                </tr>
                <?php $i++; endforeach; ?>
              </tbody>
            </table>
          </div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>