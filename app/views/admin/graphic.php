<?php if ($this->allowFile): ?>
  <!-- Content Row -->
  <div class="row main-js" my-js="graphic">
    <div class="col-lg-12">
      <!-- Bar Chart -->
          <div class="card shadow mb-4">
              <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">User Graphic</h6>
              </div>
              <div class="card-body">
                <div class="form-row">

                  <div class="form-group col-md-3 mb-3">
                    <label for="input-name">Name</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="input-name" placeholder="Name / ID" aria-describedby="btn-load-users" readonly>
                      <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="btn-load-users">Load</button>
                      </div>
                    </div>
                    <div class="invalid-feedback" id="msg-input-name"></div>
                  </div>

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

                  <div class="form-group col-md-3 my-0 my-md-auto mb-3">
                    <button type="button" id="btn-show-graphic" class="btn btn-primary btn-block mt-md-3">Show</button>
                  </div>

                </div>
                <div class="chart-bar d-none">
                  <hr>
                      <canvas id="myBarChart"></canvas>
                  </div>
              </div>
          </div>
    </div>
  </div>

  <!-- Modal View -->
  <div class="modal fade" id="modal-load-users" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Select user</h5>
        </div>
        <div class="modal-body">
          <div class="table-responsive" id="modal-response-1">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                </tr>               
              </tfoot>
              <tbody>
                <?php foreach ($data['users'] as $members) : ?>
                <tr id="u-<?= $this->e($members['id']) ?>" is-val="<?= $this->e(ucwords(strtolower($members['name']))." - ".$members['id']) ?>" data="<?= $this->w3llEncode($this->e($members['id']." - ".$members['name'])) ?>">
                  <td class="align-midle text-center">
                    <span id="set-user" target="<?= $this->e($members['id']) ?>"><?= $this->e($members['id']) ?></span>
                  </td>
                  <td>
                    <span id="set-user" target="<?= $this->e($members['id']) ?>"><?= $this->e(ucwords(strtolower($members['name']))) ?></span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>