<?=$this->extends('layout')?>

<?=$this->block('content')?>
<main class="w-3/4 mx-auto mt-8">
  <?=$this->renderBlock('top')?>

  <div class="flex">
    <section class="w-3/4 px-2">
      <?=$this->renderBlock('main')?>
    </section>
    <aside class="w-1/4 ml-auto px-10">
      <div class="sticky top-28">
        <?=$this->renderBlock('sidebar')?>
      </div>
    </aside>
  </div>
</main>
<?=$this->endBlock()?>
