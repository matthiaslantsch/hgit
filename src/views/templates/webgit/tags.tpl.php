<ul class="list-group list-group-flush">
  <?php foreach($tags as $tag): ?>
    <li class="list-group-item">
      <div>
        <h4><?=$tag->name?></h4>
        <a href="<?=linkWebgit('tree', $project, $tag->hash)?>">Browse Files</a>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
