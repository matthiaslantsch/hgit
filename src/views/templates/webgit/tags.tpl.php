<ul class="list-group list-group-flush">
  <?php foreach($tags as $tag): ?>
    <li class="list-group-item">
      <div>
        <h4><?=$tag->name?></h4>
        <a href="<?=$_urlhelper->linkTo(sprintf("%s/repo/%s/tree/%s", $project->slugname(), $repoName, $tag->hash))?>">Browse Files</a>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
