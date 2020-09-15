<div class="card">
  <div class="card-header">
    <div class="row">
      <div class="col-md-4">
        <i class="fa fa-file-code-o" aria-hidden="true"></i>
        <?=basename($blob->name)?>
      </div>
      <?php if($blob->lastCommit($context->refspec) !== null): ?>
        <div class="col-md-7">
          <a href="<?=linkWebgit('commit', $project, $blob->lastCommit($context->refspec)->hash)?>">
            <?=$blob->lastCommit()->msg?> -
            <?=$blob->lastCommit()->ago?>
          </a>
        </div>
      <?php else: ?>
        <div class="col-md-7"></div>
      <?php endif; ?>
      <div class="col-md-1">
        <a href="<?=linkWebgit('raw', $project, $context->refspec, $context->path)?>">Raw</a>
      </div>
    </div>
  </div>
    <pre class="prettyprint linenums card-block">
      <?=htmlspecialchars(trim($blob->getContent()))?>
    </pre>
</div>
