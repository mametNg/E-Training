<?php if ($this->allowFile): ?>
  <div class="row main-js" my-js="result-exam">
    <div class="col-lg-12">
      <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between" >
          <h6 class="mb-0 mt-2 font-weight-bold text-primary">Exam Results</h6>
        </div>
        <div class="card-body">
          <div class="form-row">

            <div class="form-group col-md-3 mb-3">
              <label for="input-cat-bu">Category</label>
              <select class="form-control" id="input-cat-bu">
                <option disabled selected>Choose a Category</option>
                <?php foreach ($data['type']['cat'] as $bu) : ?>
                  <option value="<?= $this->w3llEncode($this->e($bu['bu'])) ?>"><?= $this->e($bu['bu']) ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback" id="msg-input-cat-bu"></div>
            </div>

            <div class="form-group col-md-3 mb-3">
              <label for="input-cat-area">Area</label>
              <select class="form-control" disabled id="input-cat-area">
                <option disabled selected>Choose a Area</option>
              </select>
              <div class="invalid-feedback" id="msg-input-cat-area"></div>
            </div>

            <div class="form-group col-md-2 my-0 my-md-auto mb-3">
              <button type="button" id="btn-show-all-result" class="btn btn-primary btn-block mt-md-3">Show all</button>
            </div>

          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>BU</th>
                  <th>Area</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>BU</th>
                  <th>Area</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>               
              </tfoot>
              <tbody id="cos-quest">

                <?php $i=1; foreach ($data['type']['quest'] as $soal) : ?>
                <tr>
                  <td class="text-wrap align-middle text-center">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="" id="list-exam" data="<?= $this->e($soal['id']) ?>">
                      <label class="form-check-label" for="list-exam"></label>
                    </div>
                  </td>
                  <td class="text-wrap align-middle text-center"><?= $i ?></td>
                  <td><?= $this->e($soal['bu']) ?></td>
                  <td><?= $this->e($soal['area']) ?></td>
                  <td><?= $this->e($soal['cat']) ?></td>
                  <td><?= $this->e(substr(strip_tags(str_replace(['&nbsp;', 'amp;', '<br>'], "", $this->w3llDecode($soal['quest']))), 0, 78)) ?></td>
                  <td class="text-wrap align-middle text-center"><?= ($this->e($soal['status']) == true ? "Active":"Non-Active") ?></td>
                  <td class="text-wrap align-middle text-center">
                    <div class="d-flex justify-content-between">
                      <a href="#" id="list-edit-exam" data-toggle="modal" data="<?= $this->e($soal['id']) ?>" class="m-1 badge badge-success"><span class="mr-2">Edit</span><i class="fas fa-fw fa-edit"></i></a>
                      <a href="#" id="list-delete-exam" data-toggle="modal" data-target="#modal-delete-exam" data="<?= $this->e($soal['id']) ?>" class="m-1 badge badge-danger"><span class="mr-2">Delete</span><i class="fas fa-fw fa-trash"></i></a>
                      <a href="#" id="list-view-exam" data-toggle="modal" data="<?= $this->e($soal['id']) ?>" class="m-1 badge badge-info"><span class="mr-2">View</span><i class="fas fa-fw fa-eye"></i></a>
                      <?php if (strtolower($this->e($soal['status'])) == true): ?>
                        <a href="#" id="list-disable-exam" data-toggle="modal" data-target="#modal-disable-exam" data="<?= $this->e($soal['id']) ?>" class="m-1 badge badge-dark"><span class="mr-2">Disable</span><i class="fas fa-fw fa-eye"></i></a>
                      <?php elseif (strtolower($this->e($soal['status'])) == false): ?>
                        <a href="#" id="list-enable-exam" data-toggle="modal" data-target="#modal-enable-exam" data="<?= $this->e($soal['id']) ?>" class="m-1 badge badge-secondary"><span class="mr-2">Enable</span><i class="fas fa-fw fa-eye"></i></a>
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

  <!-- Modal Reset -->
  <div class="modal modal-secondary fade" id="modal-exam-reset" tabindex="-1" role="dialog" aria-labelledby="modal-exam-reset" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="text-center">
            <div class="icon text-danger">
              <i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
            </div>
            <h5 class="mt-4">Are you sure you want to Reset now!</h5>
            <p class="text-sm text-sm">You will create access to <span class="font-weight-bold" id="target-reset"></span> .</p>
          </div>
          <div class="d-flex justify-content-center">
            <div class="m-2">
              <button type="button" id="btn-exam-reset" data-info="" data-role class="btn btn-danger">Submit Now</button>
            </div>
            <div class="m-2">
              <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Reset -->
  <div class="modal modal-secondary fade" id="modal-exam-delete" tabindex="-1" role="dialog" aria-labelledby="modal-exam-delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="text-center">
            <div class="icon text-danger">
              <i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
            </div>
            <h5 class="mt-4">Are you sure you want to Deleted!</h5>
            <p class="text-sm text-sm">Exam ID <span class="target-delete font-weight-bolder"></span> data will be deleted.</p>
          </div>
          <div class="d-flex justify-content-center">
            <div class="m-2">
              <button type="button" id="btn-exam-delete" data-info="" data-role class="btn btn-danger">Submit Now</button>
            </div>
            <div class="m-2">
              <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal View -->
  <div class="modal fade" id="modal-exam-result" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
          <h5 class="modal-title" id="exampleModalLabel">Exam Result Preview</h5>
          <div>
            <div id="btn-display-h1" class="btn-group" role="group" aria-label="Basic example">
              <button type="button" id="btn-pdf-result" class="btn btn-secondary">Export to PDF</button>
              <button type="button" id="btn-print-result" class="btn btn-secondary">Print All</button>
            </div>
            <div id="btn-display-h2" class="btn-group d-none" role="group" aria-label="Basic example">
              <button type="button" id="btn-display-back" class="btn btn-secondary">Back</button>
            </div>
          </div>
        </div>
        <div class="modal-body">
          <div class="table-responsive" id="modal-response-1">
            <table class="table table-bordered" id="dataTable-modal" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>BU</th>
                  <th>Area</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>BU</th>
                  <th>Area</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>               
              </tfoot>
              <tbody id="modal-body">
              </tbody>
            </table>
          </div>

          <div class="table-responsive d-none" id="modal-response-2">
            <table class="table table-bordered" id="dataTable-3" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>Score</th>
                  <th>BU</th>
                  <th>Area</th>
                  <th>Category</th>
                  <th>Desc</th>
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
                  <th>Desc</th>
                  <th>Date</th>
                </tr>               
              </tfoot>
              <tbody id="modal-body-2">
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Static -->
  <div class="modal modal-secondary fade" id="modal-static-view" tabindex="-1" role="dialog" aria-labelledby="modal-static-view" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <canvas id="report-static" width="400" height="300"></canvas>
          <hr class="my-2">
          <div class="form-row">
            <div class="form-group col-md-3">
              <label>Start date :</label>
              <input type="date" id="start-date" class="form-control" value="<?= date("Y-m-d", (time() - (604800*2))) ?>">
            </div>
            <div class="form-group col-md-3">
              <label>End date :</label>
              <input type="date" id="end-date" class="form-control" value="<?= date("Y-m-d") ?>">
            </div>
            <div class="form-group col-md-3 mt-auto">
              <button type="button" class="btn btn-primary btn-block" id="find-static">Find</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php endif; ?>