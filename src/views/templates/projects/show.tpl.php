<?=$this->extends('includes/layout-sidebar')?>
<?=$this->block('main')?>
  <div class="py-2 mx-2">
    <?=\holonet\hgit\views\helpers\ViewFunctions::renderMarkdown($model->description ?? '')?>
  </div>
<?=$this->endBlock()?>

<?=$this->block('sidebar')?>
  <p class="my-2">
    <ul>
      <li>Project type: <?=$model->type->value?></li>

      <?php foreach($statistics as $key => $val): ?>
        <li><?=ucwords($key)?>: <?=$val?></li>
      <?php endforeach; ?>
    </ul>
  </p>
<?=$this->endBlock()?>

<?=$this->block('top')?>
<h4 class="text-3xl"><?=$model->name?></h4>

<p class="my-2">
  <?php if(\holonet\hgit\views\helpers\ViewFunctions::can($model, \holonet\hgit\models\enum\Permission::READCODE, $this->user())): ?>
    <a href="<?=$this->linkTo('webgit_show', ['slug' => $model->slug, 'repository' => "{$model->slug}.git"])?>">Git Repository</a>
  <?php endif; ?>
</p>

<?php if(\holonet\hgit\views\helpers\ViewFunctions::can($model, \holonet\hgit\models\enum\Permission::ADMIN, $this->user())): ?>
  <a href="<?=$this->linkREST($model, 'edit')?>">Edit project specification</a>
<?php endif; ?>

<hr class="my-2">
<?=$this->endBlock()?>
