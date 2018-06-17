<div class="card">
  <div class="card-header">
    <h4 class="card-title">
      <?=$commit->hash?>
    </h4>
    <p><a href="<?=linkTo("user/{$commit->author}")?>"><?=$commit->author?></a> (<?=$commit->ago?>)</p>
    <p><?=$commit->msg?></p>
    <a href="<?=linkTo(sprintf("%s/git/tree/%s", $project->slugname(), $commit->hash))?>">Browse Files</a>
  </div>
  <div class="card-block">
    <ul class="list-unstyled">
      <?php foreach($commit->details() as $change): ?>
        <li class="filterable">
          <?php if($change->type == "A"): ?>
            Created new file
          <?php else: ?>
            Changed file
          <?php endif; ?>
          <a class="filter_crit"
            href="<?=linkTo(sprintf("%s/git/blob/%s/%s", $project->slugname(), $commit->hash, urlencodeall($change->newBlob->name)))?>">
            <?=basename($change->newBlob->name)?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <hr>
    <?=highlightCode($commit->getDiff(), "diff")?>
  </div>
</div>
