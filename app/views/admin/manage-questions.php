<?php if ($this->allowFile): ?>
  <div class="row main-js" my-js="manage-questions">
    <div class="col-lg-12">
      <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between" >
          <h6 class="mb-0 mt-2 font-weight-bold text-primary">Manage Questions</h6>
          <button class="btn btn-primary btn-small" data-toggle="modal" data-target="#modal-new-exam">Add Question</button>
        </div>
        <div class="card-body">
          <div class="form-row">

            <div class="form-group col-md-3 mb-3">
              <label for="input-cat-bu">Bu</label>
              <select class="form-control" id="input-cat-bu">
                <option disabled selected>Choose a Bu</option>
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

            <div class="form-group col-md-3 mb-3">
              <label for="input-cat-bu">Category</label>
              <select class="form-control" disabled id="input-cat-cat">
                <option disabled selected>Choose a Category</option>
              </select>
              <div class="invalid-feedback" id="msg-input-cat-bu"></div>
            </div>

            <div class="form-group col-md-2 my-0 my-md-auto mb-3">
              <button type="button" id="btn-show-all-quest" class="btn btn-primary btn-block mt-md-3">Show all</button>
            </div>

          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Check</th>
                  <th>No</th>
                  <th>BU</th>
                  <th>Area</th>
                  <th>Category</th>
                  <th>Question</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>Check</th>
                  <th>No</th>
                  <th>BU</th>
                  <th>Area</th>
                  <th>Category</th>
                  <th>Question</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>               
              </tfoot>
              <tbody id="cos-quest">

                <?php $i=1; foreach ($data['soal'] as $soal) : ?>
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

  <!-- Modal Enable exam -->
  <div class="modal modal-secondary fade" id="modal-enable-exam" tabindex="-1" role="dialog" aria-labelledby="modal-enable-exam" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="text-center">
            <div class="icon text-seconfary">
              <i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
            </div>
            <h5 class="mt-4">Are you sure you want to enable it now!</h5>
            <p class="text-sm text-sm">Exam ID <span class="info-enable-exam font-weight-bolder"></span> all data will be enabled.</p>
          </div>
          <div class="d-flex justify-content-center">
            <div class="m-2">
              <button type="button" id="save-enable-exam" data-info="" data-role class="btn btn-seconfary">Enable Now</button>
            </div>
            <div class="m-2">
              <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Disable exam -->
  <div class="modal modal-secondary fade" id="modal-disable-exam" tabindex="-1" role="dialog" aria-labelledby="modal-disable-exam" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="text-center">
            <div class="icon text-seconfary">
              <i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
            </div>
            <h5 class="mt-4">Are you sure you want to disable it now!</h5>
            <p class="text-sm text-sm">Exam ID <span class="info-disable-exam font-weight-bolder"></span> all data will be disabled.</p>
          </div>
          <div class="d-flex justify-content-center">
            <div class="m-2">
              <button type="button" id="save-disable-exam" data-info="" data-role class="btn btn-seconfary">Disable Now</button>
            </div>
            <div class="m-2">
              <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Delete exam -->
  <div class="modal modal-secondary fade" id="modal-delete-exam" tabindex="-1" role="dialog" aria-labelledby="modal-delete-exam" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="text-center">
            <div class="icon text-danger">
              <i class="fas fa-exclamation-circle fa-3x opacity-8"></i>
            </div>
            <h5 class="mt-4">Are you sure you want to delete it now!</h5>
            <p class="text-sm text-sm">Exam ID <span class="info-delete-exam font-weight-bolder"></span> all data will be deleted.</p>
          </div>
          <div class="d-flex justify-content-center">
            <div class="m-2">
              <button type="button" id="save-delete-exam" data-info="" data-role class="btn btn-danger">Delete Now</button>
            </div>
            <div class="m-2">
              <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal View Exam -->
  <div class="modal fade" id="modal-view-exam" tabindex="-1" aria-labelledby="modal-view-exam" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">View Exam</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <div id="exam-ok">
            <p class="font-weight-bolder mb-0">Soal : </p>
            <p class="text-muted mb-0" id="exam-quest"></p>
            <div class="my-2 text-center d-none" id="exam-box-img">
              <img id="exam-img" src="" class="img-thumbnail text-center">
            </div>
            <p class="font-weight-bolder mb-0">Pilihan : </p>
            <ol type="A" class="text-muted" id="exam-answers"></ol>
            <p class="font-weight-bolder mb-0">Jawaban : <span class="text-muted" id="exam-answer"></span></p>
          </div>

          <div id="exam-error">
            
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal New Exam-->
  <div class="modal fade" id="modal-new-exam" tabindex="-1" aria-labelledby="modal-new-exam" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Question</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="input-bu">BU</label>
              <input type="text" name="input-bu" id="input-bu" class="form-control" placeholder="BU" required>
              <div class="invalid-feedback" id="msg-input-bu"></div>
            </div>
            
            <div class="form-group col-md-6">
              <label for="input-area">Area</label>
              <input type="text" name="input-area" id="input-area" class="form-control" placeholder="Area" required>
              <div class="invalid-feedback" id="msg-input-area"></div>
            </div>
          </div>


          <div class="form-group">
            <label for="input-category">Category</label>
            <input type="text" name="input-category" id="input-category" class="form-control" placeholder="Category" required>
              <div class="invalid-feedback" id="msg-input-category"></div>
          </div>

          <div class="form-group">
            <label for="input-pertanyaan">Question</label>
            <textarea name="pertanyaan" class="form-control" id="input-pertanyaan" rows="10" cols="80" placeholder="Pertanyaan" required></textarea>
            <div class="invalid-feedback" id="msg-input-pertanyaan"></div>
          </div>

          <div class="form-group">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="turn-new-image">
              <label class="custom-control-label" for="turn-new-image" id="label-turn-new-image">Enable change image</label>
            </div>
          </div>
          <div class="form-row mb-2 align-items-center">
            <div class="form-group col-4 col-lg-2">
              <img id="new-img-thumbnail" src="<?= $this->base_url('assets/img/account/default.jpg') ?>" class="img-thumbnail"> 
            </div>

            <div class="form-group col-8 col-lg-10 pl-lg-4">
              <label for="input-new-image">Image</label>
              <input type="file" accept="image/*" id="input-new-image" data-choose="new" class="custom-input-file" disabled>
              <label for="input-new-image">
                <i class="fa fa-upload"></i>
                <span class="new-file-name">Choose a image</span>
              </label>
              <div class="invalid-feedback" id="msg-input-new-image"></div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-3">
              <label for="input-answer-a">Answer A</label>
              <input type="text" name="input-answer-a" id="input-answer-a" class="form-control" placeholder="Answer A" required>
              <div class="invalid-feedback" id="msg-input-answer-a"></div>
            </div>

            <div class="form-group col-md-3">
              <label for="input-answer-b">Answer B</label>
              <input type="text" name="input-answer-b" id="input-answer-b" class="form-control" placeholder="Answer B" required>
              <div class="invalid-feedback" id="msg-input-answer-b"></div>
            </div>
            <div class="form-group col-md-3">
              <label for="input-answer-c">Answer C</label>
              <input type="text" name="input-answer-c" id="input-answer-c" class="form-control" placeholder="Answer C" required>
              <div class="invalid-feedback" id="msg-input-answer-c"></div>
            </div>

            <div class="form-group col-md-3">
              <label for="input-answer-d">Answer D</label>
              <input type="text" name="input-answer-d" id="input-answer-d" class="form-control" placeholder="Answer D" required>
              <div class="invalid-feedback" id="msg-input-answer-d"></div>
            </div>

          </div>

          <div class="form-group">
            <label for="input-answer-key">Answer Key</label>
            <select class="form-control" id="input-answer-key">
              <option selected="" disabled="">choose a answer key</option>
              <option>A</option>
              <option>B</option>
              <option>C</option>
              <option>D</option>
            </select>
            <div class="invalid-feedback" id="msg-input-answer-key"></div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="add-new-exam">Save changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit Exam-->
  <div class="modal fade" id="modal-edit-exam" target="" tabindex="-1" aria-labelledby="modal-edit-exam" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Question</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="input-edit-bu">BU</label>
              <input type="text" name="input-edit-bu" id="input-edit-bu" class="form-control" placeholder="BU" required>
              <div class="invalid-feedback" id="msg-input-edit-bu"></div>
            </div>
            
            <div class="form-group col-md-6">
              <label for="input-edit-area">Area</label>
              <input type="text" name="input-edit-area" id="input-edit-area" class="form-control" placeholder="Area" required>
              <div class="invalid-feedback" id="msg-input-edit-area"></div>
            </div>
          </div>

          <div class="form-group">
            <label for="input-edit-category">Category</label>
            <input type="text" name="input-edit-category" id="input-edit-category" class="form-control" placeholder="Category" required>
              <div class="invalid-feedback" id="msg-input-edit-category"></div>
          </div>

          <div class="form-group">
            <label for="input-edit-pertanyaan">Question</label>
            <textarea name="pertanyaan" class="form-control" id="input-edit-pertanyaan" rows="10" cols="80" placeholder="Pertanyaan" required></textarea>
            <div class="invalid-feedback" id="msg-input-edit-pertanyaan"></div>
          </div>

          <div class="form-group">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="turn-edit-image">
              <label class="custom-control-label" for="turn-edit-image" id="label-turn-edit-image">Enable change image</label>
            </div>
          </div>
          <div class="form-row mb-2 align-items-center">
            <div class="form-group col-4 col-lg-2">
              <img id="edit-img-thumbnail" src="<?= $this->base_url('assets/img/account/default.jpg') ?>" class="img-thumbnail"> 
            </div>

            <div class="form-group col-8 col-lg-10 pl-lg-4">
              <label for="input-edit-image">Image</label>
              <input type="file" accept="image/*" id="input-edit-image" data-choose="change" class="custom-input-file" disabled>
              <label for="input-edit-image">
                <i class="fa fa-upload"></i>
                <span class="change-file-name">Choose a image</span>
              </label>
              <div class="invalid-feedback" id="msg-input-edit-image"></div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-3">
              <label for="input-edit-answer-a">Answer A</label>
              <input type="text" name="input-edit-answer-a" id="input-edit-answer-a" class="form-control" placeholder="Answer A" required>
              <div class="invalid-feedback" id="msg-input-edit-answer-a"></div>
            </div>

            <div class="form-group col-md-3">
              <label for="input-edit-answer-b">Answer B</label>
              <input type="text" name="input-edit-answer-b" id="input-edit-answer-b" class="form-control" placeholder="Answer B" required>
              <div class="invalid-feedback" id="msg-input-edit-answer-b"></div>
            </div>
            <div class="form-group col-md-3">
              <label for="input-edit-answer-c">Answer C</label>
              <input type="text" name="input-edit-answer-c" id="input-edit-answer-c" class="form-control" placeholder="Answer C" required>
              <div class="invalid-feedback" id="msg-input-edit-answer-c"></div>
            </div>

            <div class="form-group col-md-3">
              <label for="input-edit-answer-d">Answer D</label>
              <input type="text" name="input-edit-answer-d" id="input-edit-answer-d" class="form-control" placeholder="Answer D" required>
              <div class="invalid-feedback" id="msg-input-edit-answer-d"></div>
            </div>

          </div>

          <div class="form-group">
            <label for="input-edit-answer-key">Answer Key</label>
            <select class="form-control" id="input-edit-answer-key">
              <option class="select-def" selected="" disabled="">choose a answer key</option>
              <option class="select-a">A</option>
              <option class="select-b">B</option>
              <option class="select-c">C</option>
              <option class="select-d">D</option>
            </select>
            <div class="invalid-feedback" id="msg-input-edit-answer-key"></div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="add-edit-exam">Save changes</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>