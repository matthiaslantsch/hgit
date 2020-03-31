<h3>Project Overview</h3>
<?php if(isset($session_user)): ?>
  <a href="<?=linkTo("projects/new")?>">Create a new project</a>
<?php endif; ?>
<hr>
<?php if(!empty($projects)): ?>
  <?php foreach($projects as $proj): ?>
    <div class="row">
      <div class="col-md-9">
        <h4><?=$proj->name?></h4>
        <!--<a class="nav-link" href="<?=linkTo("projects/{$proj->slugname()}")?>"><i class="fa fa-feed"></i> Activity</a>-->
        <?php if(isAllowedAction($proj, "readCode", $session_user ?? null)): ?>
          <a class="nav-link" href="<?=linkTo("{$proj->slugname()}/git")?>"><i class="fa fa-code-fork"></i> Git Repository</a>
        <?php endif; ?>
      </div>
      <div class="col-md-3">
        <ul class="list-unstyled">
          <?php foreach($statistics[$proj->id] ?? array() as $key => $val): ?>
            <li><?=ucwords($key)?>: <?=$val?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <hr>
  <?php endforeach; ?>
<?php else: ?>
  There seems to be nothing here :(
  <?php if(isset($session_user)): ?>
    <a href="<?=linkTo("projects/new")?>">Create a new project</a>
  <?php endif; ?>
<?php endif; ?>
