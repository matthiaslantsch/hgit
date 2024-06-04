<?=$this->extends('layout')?>
<?=$this->block('content')?>
<div class="w-2/3 mx-auto flex mt-8">
  <main class="w-3/4 flex px-2">
    <?=$this->renderBlock('main')?>
  </main>
  <aside class="w-1/4 ml-auto px-2">
    <div class="sticky top-28">
      <?=$this->renderBlock('sidebar')?>
    </div>
  </aside>
</div>
<?=$this->endBlock()?>
