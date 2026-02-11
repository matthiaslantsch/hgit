<?=$this->extends('webgit/webgit')?>
<?=$this->block('main')?>
<ul>
  <?php foreach($tags as $tag): ?>
    <li class="my-2">
      <div>
        <h4 class="text-3xl font-bold">
          <?=$tag->name?>
        </h4>
        <a href="<?=$this->linkTo('webgit_tree', $context->routeParameters(ref: $tag->hash))?>">Browse Files</a>
      </div>
      <hr>
    </li>
  <?php endforeach; ?>
</ul>
<?=$this->endBlock();?>
