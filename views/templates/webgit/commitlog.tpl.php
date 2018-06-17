<ul class="list-group list-group-flush">
  <?php foreach($gitlog as $commit): ?>
    <li class="list-group-item">
      <div>
        <h4>
          <a href="<?=linkTo(sprintf("%s/git/commit/%s", $project->slugname(), $commit->hash))?>"><?=$commit->hash?></a>
        </h4>
        <p><a href="<?=linkTo("user/{$commit->author}")?>"><?=$commit->author?></a> (<?=$commit->ago?>)</p>
        <p><?=$commit->msg?></p>
        <a href="<?=linkTo(sprintf("%s/git/tree/%s", $project->slugname(), $commit->hash))?>">Browse Files</a>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
