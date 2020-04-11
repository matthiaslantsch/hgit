<table class="table">
  <thead>
    <tr>
      <td></td>
      <td colspan="2">Filename</td>
      <td>Type</td>
      <td>Size</td>
      <td colspan="4">Last change</td>
      <td></td>
    </tr>
  </thead>
  <tbody>
    <?php if(!empty($context->path)): ?>
      <tr>
        <td><i class="fa fa-level-up" aria-hidden="true"></i></td>
        <td><a href="<?=$_urlhelper->linkTo(sprintf("%s/git/tree/%s/%s", $project->slugname(), $context->refspec, dirname($context->path)))?>">..</a></td>
      </tr>
    <?php endif; ?>
    <?php foreach ($treeList as $object): ?>
      <tr class="filterable">
        <td>
          <?php if($object->type() == "blob"): ?>
            <i class="fa fa-file-code-o" aria-hidden="true"></i>
          <?php elseif($object->type() == "tree"): ?>
            <i class="fa fa-folder-o" aria-hidden="true"></i>
          <?php endif; ?>
        </td>
        <td class="filter_crit" colspan="2">
          <a href="<?=$_urlhelper->linkTo(sprintf("%s/git/%s/%s/%s", $project->slugname(), $object->type(), $context->refspec, urlencodeall($object->name)))?>">
            <?=basename($object->name)?>
          </a>
        </td>
        <td><?=$object->type()?></td>
        <td><?=($object->type() == "blob" ? $object->getFileSize() : "")?></td>
        <td colspan="4">
          <a href="<?=$_urlhelper->linkTo(sprintf("%s/git/commit/%s", $project->slugname(), $object->lastCommit($context->refspec)->hash))?>">
            <?=$object->lastCommit($context->refspec)->msg?>
          </a>
        </td>
        <td><?=$object->lastCommit($context->refspec)->ago?></td>
    <?php endforeach; ?>
  </tbody>
</table>
