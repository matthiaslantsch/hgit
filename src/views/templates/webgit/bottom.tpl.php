  </div>
  <div class="col-sm-2">
    <?php foreach($branches as $key => $brnchs): ?>
      <div class="sidebar-module sidebar-module-inset">
        <h4><?=ucfirst($key)?>:</h4>
       <?php foreach($brnchs as $branch): ?>
          <p><a href="<?=linkWebgit($page, $project, urlencodeall($branch), $context->path)?>">
            <?=$branch?>
          </a></p>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js?skin=desert"></script>
