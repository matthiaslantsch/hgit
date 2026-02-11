<?=$this->extends('includes/layout-sidebar')?>
<?=$this->block('top')?>
<?php if($page === 'blob' || $page === 'commit'): ?>
  <script src="https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js?skin=desert"></script>
<?php endif; ?>

<h3 class="text-lg font-semibold mb-4">
  Git viewing
  <a href="<?=$this->linkREST($context->project)?>" class="text-blue-600 hover:underline">
    <?=$context->project->name?>
  </a>
  /
  <a href="<?=$this->linkTo('webgit_tree', $context->routeParameters(ref: $context->ref))?>"
     class="text-blue-600 hover:underline">
    <?=$context->ref?>
  </a>
  <?php if(strlen($context->path) > 0): ?>
    / <?=$context->path?>
  <?php endif;?>
</h3>

<ul class="flex border-b mb-6">
  <li class="flex-1 text-center">
    <a
      class="block py-2 border-b-2 <?=($page == 'tree' || $page == 'blob'
        ? 'text-blue-600 font-semibold'
        : 'border-transparent text-gray-300 hover:text-blue-600')?>"
      href="<?=$this->linkTo('webgit_tree', $context->routeParameters(ref: $linkToBranch))?>">
      Files
    </a>
  </li>
  <li class="flex-1 text-center">
    <a
      class="block py-2 border-b-2 <?=($page == 'log'
        ? 'text-blue-600 font-semibold'
        : 'border-transparent text-gray-300 hover:text-blue-600')?>"
      href="<?=$this->linkTo('webgit_log', $context->routeParameters(ref: $linkToBranch))?>">
      Commits
    </a>
  </li>
  <li class="flex-1 text-center">
    <a
      class="block py-2 border-b-2 <?=($page == 'tags'
        ? 'text-blue-600 font-semibold'
        : 'border-transparent text-gray-300 hover:text-blue-600')?>"
      href="<?=$this->linkTo('webgit_tags', $context->routeParameters())?>">
      Tags
    </a>
  </li>
</ul>

<div class="flex flex-wrap -mx-2 mb-8">
  <div class="w-full lg:w-1/2 px-2 mb-4 lg:mb-0">
    <label class="block text-sm font-medium text-gray-300 mb-1">
      Filter
      <input
        type="text"
        id="filterTf"
        class="w-full shadow appearance-none border border-gray-700 border-opacity-80 shadow-sm focus:outline-none focus:ring-blue-500
            focus:border-blue-500 rounded py-2 px-3 text-white bg-gray-900 w-full text-white mt-2"
        placeholder="Type to filter">
    </label>
  </div>

  <div class="w-full lg:w-1/2 px-2">
    <label class="block text-sm font-medium text-gray-300 mb-1">
      Clone URL
      <div class="flex items-stretch" x-data>
        <input type="text" x-ref="cloneUrl" readonly value="<?=$cloneUrl?>" class="block w-full border border-gray-700 border-opacity-80 shadow-sm focus:outline-none focus:ring-blue-500
            focus:border-blue-500 rounded py-2 px-3 text-white bg-gray-900 w-full text-white mt-2" />
        <button type="button" class="inline-flex items-center px-3 py-2" @click="navigator.clipboard.writeText($refs.cloneUrl.value)">Copy</button>
      </div>
    </label>
  </div>
</div>
<?=$this->endBlock()?>
<?=$this->block('sidebar')?>
<p class="my-2">
  <?php foreach($branches as $key => $list): ?>
    <?php if (empty($list)) continue; ?>
    <h4 class="font-semibold"><?=ucfirst($key)?>:</h4>
    <ul>
      <?php foreach($list as $branch): ?>
        <li>
          <a href="<?=$this->linkTo("webgit_{$page}", $context->routeParameters(urlencode($branch), $context->path))?>">
            <?=$branch?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endforeach; ?>
</p>
<?=$this->endBlock()?>
