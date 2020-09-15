<ul class="list-group list-group-flush">
  <?php foreach($gitlog as $commit): ?>
    <li class="list-group-item">
      <div>
        <h4>
          <a href="<?=linkWebgit('commit', $project, $commit->hash)?>"><?=$commit->hash?></a>
        </h4>
        <p><?=$commit->author?> (<?=$commit->ago?>)</p>
        <p><?=$commit->msg?></p>
        <a href="<?=linkWebgit('tree', $project, $commit->hash)?>">Browse Files</a>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
