<ul class="list-group list-group-flush">
  <?php foreach($gitlog as $commit): ?>
    <li class="list-group-item">
      <div>
        <h4>
          <a href="<?=$_urlhelper->linkTo(sprintf("%s/repo/%s/commit/%s", $project->slugname(), $repoName, $commit->hash))?>"><?=$commit->hash?></a>
        </h4>
        <p><a href="<?=$_urlhelper->linkTo("user/{$commit->author}")?>"><?=$commit->author?></a> (<?=$commit->ago?>)</p>
        <p><?=$commit->msg?></p>
        <a href="<?=$_urlhelper->linkTo(sprintf("%s/repo/%s/tree/%s", $project->slugname(), $repoName, $commit->hash))?>">Browse Files</a>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
