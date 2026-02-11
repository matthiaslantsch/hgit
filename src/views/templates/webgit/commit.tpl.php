<?=$this->extends('webgit/webgit')?>
<?=$this->block('main')?>
<div class="bg-gray-700 border border-gray-600 rounded shadow">
  <div class="border-b border-gray-500 px-4 py-2 bg-gray-600">
    <h4 class="text-lg font-semibold">
      <?=$commit->hash?>
    </h4>

    <p class="text-sm">
      <?=$commit->author?> (<?=$commit->ago?>)
    </p>

    <p class="mt-1">
      <?=$commit->msg?>
    </p>

    <a
      class="inline-block mt-2 text-blue-600 hover:underline"
      href="<?=$this->linkTo('webgit_tree', $context->routeParameters(ref: $commit->hash))?>"
    >
      Browse Files
    </a>
  </div>

  <div class="p-4">
    <ul class="space-y-4">
      <?php foreach($commit->details() as $change): ?>
        <li class="filterable">
          <?php if($change->type !== "D"): ?>
            <?php if($change->type == "A"): ?>
              <span class="font-medium text-green-600">Created new file</span>
            <?php else: ?>
              <span class="font-medium text-yellow-600">Changed file</span>
            <?php endif; ?>

            <a
              class="filter_crit ml-1 text-blue-600 hover:underline"
              href="<?=$this->linkTo('webgit_blob', $context->routeParameters(ref: $commit->hash, path: $change->newBlob->name))?>"
            >
              <?=basename($change->newBlob->name)?>
            </a>

            <div class="mt-2">
              <?=\holonet\hgit\views\helpers\ViewFunctions::highlightCode($change->getDiff(), "diff")?>
            </div>
          <?php else: ?>
            <span class="font-medium text-red-600">Removed file</span>
            <a
              class="filter_crit ml-1 text-blue-600 hover:underline"
              href="<?=$this->linkTo('webgit_blob', $context->routeParameters(ref: $commit->parenthash, path: $change->oldBlob->name))?>"
            >
              <?=basename($change->oldBlob->name)?>
            </a>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <hr class="mt-6 border-gray-200">
  </div>
</div>
<?=$this->endBlock();?>
