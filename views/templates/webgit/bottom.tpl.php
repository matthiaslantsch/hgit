  </div>
  <div class="col-sm-2">
    <?php foreach($branches as $key => $brnchs): ?>
      <div class="sidebar-module sidebar-module-inset">
        <h4><?=ucfirst($key)?>:</h4>
       <?php foreach($brnchs as $branch): ?>
          <p><a href="<?=linkTo(sprintf("%s/git/%s/%s/%s", $project->slugname(), $page, $branch, $path))?>">
            <?=$project->name.'/'.$branch?>
          </a></p>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
