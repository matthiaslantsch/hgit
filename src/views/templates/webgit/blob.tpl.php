<?=$this->extends('webgit/webgit')?>
<?=$this->block('main')?>
<div class="bg-gray-700 border border-gray-600 rounded shadow">
  <div class="border-b border-gray-500 px-4 py-2 bg-gray-600">
    <div class="flex items-center text-sm">
      <div class="w-4/12 flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
        <span><?=basename($blob->name)?></span>
      </div>

      <?php if($blob->lastCommit($context->ref) !== null): ?>
        <div class="w-7/12 truncate">
          <a
            class="text-blue-600 hover:underline"
            href="<?=$this->linkTo('webgit_commit', $context->routeParameters(ref: $blob->lastCommit($context->ref)->hash))?>"
          >
            <?=$blob->lastCommit()->msg?> -
            <?=$blob->lastCommit()->ago?>
          </a>
        </div>
      <?php else: ?>
        <div class="w-7/12"></div>
      <?php endif; ?>

      <div class="w-1/12 text-right">
        <a
          class="text-blue-600 hover:underline"
          href="<?=$this->linkTo('webgit_raw', $context->routeParameters(ref: $context->ref, path: $context->path))?>"
        >
          Raw
        </a>
      </div>
    </div>
  </div>

  <pre class="prettyprint linenums p-4 overflow-auto text-sm bg-gray-600"><?=htmlspecialchars(trim($blob->getContent()))?></pre>
</div>



<?=$this->endBlock();?>
