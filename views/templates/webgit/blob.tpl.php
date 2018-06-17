<div class="card">
  <div class="card-header">
    <div class="row">
      <div class="col-md-4">
        <i class="fa fa-file-code-o" aria-hidden="true"></i>
        <?=basename($blob->name)?>
      </div>
      <?php if($blob->lastCommit() !== null): ?>
        <div class="col-md-7">
          <a href="<?=linkTo(sprintf("%s/git/commit/%s", $project->slugname(), $blob->lastCommit()->hash))?>">
            <?=$blob->lastCommit()->msg?> -
            <?=$blob->lastCommit()->ago?>
          </a>
        </div>
      <?php else: ?>
        <div class="col-md-7"></div>
      <?php endif; ?>
      <div class="col-md-1">
        <a href="<?=linkTo(sprintf("%s/git/raw/%s/%s", $project->slugname(), $refspec, urlencodeall($path)))?>">Raw</a>
      </div>
    </div>
  </div>
  <div class="card-block">
    <?=highlightCode($blob->getContent())?>
  </div>
</div>