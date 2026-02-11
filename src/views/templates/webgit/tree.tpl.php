<?=$this->extends('webgit/webgit')?>
<?=$this->block('main')?>
<table class="min-w-full border border-gray-300 text-sm text-white">
  <thead class="border-b border-gray-300">
    <tr class="text-left font-semibold">
      <td class="px-3 py-2"></td>
      <td class="px-3 py-2" colspan="2">Filename</td>
      <td class="px-3 py-2">Type</td>
      <td class="px-3 py-2">Size</td>
      <td class="px-3 py-2" colspan="4">Last change</td>
      <td class="px-3 py-2"></td>
    </tr>
  </thead>

  <tbody class="divide-y divide-gray-200">
  <?php if(!empty($context->path)): ?>
    <tr>
      <td class="px-3 py-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
        </svg>
      </td>
      <td class="px-3 py-2" colspan="2">
        <a
          href="<?=rtrim($this->linkTo('webgit_tree', $context->routeParameters($context->ref, dirname($context->path))), '/')?>"
          class="text-blue-600 hover:underline">
          ..
        </a>
      </td>
    </tr>
  <?php endif; ?>

  <?php usort($treeList, fn (\holonet\hgit\utils\phphgit\objects\GitObject $a, \holonet\hgit\utils\phphgit\objects\GitObject $b) => $a->type() === 'blob' ? 1 : ($b->type() === 'blob' ? -1 : 0)) ?>
  <?php foreach ($treeList as $object): ?>
    <tr class="text-white">
      <td class="px-3 py-2">
        <?php if($object->type() == "blob"): ?>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
          </svg>
        <?php elseif($object->type() == "tree"): ?>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
          </svg>
        <?php endif; ?>
      </td>

      <td class="filter_crit px-3 py-2" colspan="2">
        <a href="<?=$this->linkTo("webgit_{$object->type()}", $context->routeParameters($context->ref, $object->name))?>"
          class="text-blue-600 hover:underline">
          <?=basename($object->name)?>
        </a>
      </td>

      <td class="px-3 py-2">
        <?=$object->type()?>
      </td>

      <td class="px-3 py-2">
        <?=($object->type() == "blob" ? $object->getFileSize() : "")?>
      </td>

      <td class="px-3 py-2" colspan="4">
        <a href="<?=$this->linkTo('webgit_commit', $context->routeParameters($object->lastCommit($context->ref)->hash))?>"
          class="text-blue-600 hover:underline">
          <?=$object->lastCommit($context->ref)->msg?>
        </a>
      </td>

      <td class="px-3 py-2 whitespace-nowrap">
        <?=$object->lastCommit($context->ref)->ago?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?=$this->endBlock()?>
