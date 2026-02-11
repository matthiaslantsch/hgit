<?=$this->extends('webgit/webgit')?>
<?=$this->block('main')?>
<ul>
  <?php foreach($gitlog as $commit): ?>
    <li class="my-2">
      <div>
        <h4 class="text-3xl font-bold">
          <a href="<?=$this->linkTo('webgit_commit', $context->routeParameters(ref: $commit->hash))?>"><?=$commit->hash?></a>
        </h4>
        <p><?=$commit->author?> (<?=$commit->ago?>)</p>
        <p><?=$commit->msg?></p>
        <a href="<?=$this->linkTo('webgit_tree', $context->routeParameters(ref: $commit->hash))?>">Browse Files</a>
      </div>
      <hr>
    </li>
  <?php endforeach; ?>
</ul>
<?=$this->endBlock();?>
