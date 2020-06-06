<div class="card">
  <div class="card-header">
    <h4 class="card-title">
      <?=$commit->hash?>
    </h4>
    <p><a href="<?=$_urlhelper->linkTo("user/{$commit->author}")?>"><?=$commit->author?></a> (<?=$commit->ago?>)</p>
    <p><?=$commit->msg?></p>
    <a href="<?=$_urlhelper->linkTo(sprintf("%s/repo/%s/tree/%s", $project->slugname(), $repoName, $commit->hash))?>">Browse Files</a>
  </div>
  <div class="card-block">
    <ul class="list-unstyled">
      <?php foreach($commit->details() as $change): ?>
        <li class="filterable">
          <?php if($change->type !== "D"): ?>
            <?php if($change->type == "A"): ?>
              Created new file
            <?php else: ?>
              Changed file
            <?php endif; ?>
            <a class="filter_crit"
              href="<?=$_urlhelper->linkTo(sprintf("%s/repo/%s/blob/%s/%s", $project->slugname(), $repoName, $commit->hash, $change->newBlob->name))?>">
              <?=basename($change->newBlob->name)?>
            </a>
            <?=highlightCode($change->getDiff(), "diff")?>
          <?php else: ?>
            Removed file
            <a class="filter_crit"
               href="<?=$_urlhelper->linkTo(sprintf("%s/repo/%s/blob/%s/%s", $project->slugname(), $repoName, $commit->parenthash, $change->oldBlob->name))?>">
              <?=basename($change->oldBlob->name)?>
            </a>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <hr>
  </div>
</div>
