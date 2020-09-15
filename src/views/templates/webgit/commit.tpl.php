<div class="card">
  <div class="card-header">
    <h4 class="card-title">
      <?=$commit->hash?>
    </h4>
    <p><?=$commit->author?>(<?=$commit->ago?>)</p>
    <p><?=$commit->msg?></p>
    <a href="<?=linkWebgit('tree', $project, $commit->hash)?>">Browse Files</a>
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
              href="<?=linkWebgit('blob', $project, $commit->hash, $change->newBlob->name)?>">
              <?=basename($change->newBlob->name)?>
            </a>
            <?=highlightCode($change->getDiff(), "diff")?>
          <?php else: ?>
            Removed file
            <a class="filter_crit"
               href="<?=linkWebgit('blob', $project, $commit->parenthash, $change->oldBlob->name)?>">
              <?=basename($change->oldBlob->name)?>
            </a>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <hr>
  </div>
</div>
