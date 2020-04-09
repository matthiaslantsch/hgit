<h3>Create a new project</h3>
<hr>
<form class="formChecker" method="POST" action="<?=linkTo("projects")?>">
  <div class="form-group row">
    <label for="inputName" class="col-sm-2 col-form-label">Project Name</label>
    <div class="col-sm-10">
      <input type="text" pattern="^[_A-z0-9/]{1,}$" maxlength="20" class="form-control" id="inputName" name ="name" placeholder="Project Name" required>
    </div>
    <div class="col-sm-10 form-control-feedback"></div>
  </div>
  <hr>
  <div class="form-group row">
    <label for="inputDesc" class="col-sm-2 col-form-label">Project Description</label>
    <div class="col-sm-10">
      <textarea class="form-control simplemde" id="inputDesc" name="description" placeholder="Project Description"></textarea>
    </div>
  </div>
  <hr>
  <div class="form-group row">
	  <legend class="col-form-legend col-sm-2">Project Type</legend>
	  <div class="col-sm-10">
		  <div class="form-check">
		  <?php foreach($projectTypes as $type): ?>
			  <label class="form-check-label">
				<input class="form-check-input" type="radio" name="projectType" value="<?=$type?>">
			    <?=$type?>
			  </label>
	  	<?php endforeach; ?>
		</div>
	  </div>
  </div>
  <hr>
  <div class="form-group row">
    <legend class="col-form-legend col-sm-2">Permission Presets</legend>
    <div class="col-sm-10">
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="permPreset" value="public" checked>
          <i class="fa fa-globe"></i> Public
        </label>
        <small class="form-text text-muted">
          Everyone can read/access code and documentation, write access is limited to authenticated users.
        </small>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="permPreset" value="internal">
          <i class="fa fa-users"></i> Internal
        </label>
        <small class="form-text text-muted">
          The project can only be accessed by authenticated users.
        </small>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="permPreset" value="private">
          <i class="fa fa-ban"></i> Private
        </label>
        <small class="form-text text-muted">
          Access to the project has to be granted to every user manually.
        </small>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <div class="offset-sm-2 col-sm-10">
      <button type="submit" class="btn btn-success">Create Project</button>
    </div>
  </div>
</form>
