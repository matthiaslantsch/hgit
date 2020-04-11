<h3><?=$project->name?></h3>
<?php if(isAllowedAction($project, "readCode", $session_user ?? null)): ?>
  <a class="nav-link" href="<?=$_urlhelper->linkTo("{$project->slugname()}/git")?>"><i class="fa fa-code-fork"></i> Git Repository</a>
<?php endif; ?>
<hr>
<div class="row">
  <div class="col-md-9">
    <?=renderMarkdown($project->description)?>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-header">
        <ul class="list-unstyled">
          <?php foreach($statistics as $key => $val): ?>
            <li><?=ucwords($key)?>: <?=$val?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="card-block">
        <!--
          <?php if(isAllowedAction($project, "admin", $session_user ?? null)): ?>
            <a href="<?=$_urlhelper->linkTo("{$project->slugname()}/edit")?>" class="btn btn-block btn-secondary">Edit project Specification</a>
            <a href="<?=$_urlhelper->linkTo("{$project->slugname()}/access")?>" class="btn btn-block btn-secondary">Edit project Permissions</a>
          <?php endif; ?>
        -->
      </div>
    </div>
  </div>
</div>
