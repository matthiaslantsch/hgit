<?=$this->extends('includes/layout-sidebar')?>
<?=$this->block('main')?>
  <h3 class="text-xl mb-2">Project Overview</h3>
  <?php if(!empty($projects)): ?>
    <?php foreach($projects as $proj): ?>
      <div class="py-2">
        <a href="<?=$this->linkTo('projects_show', ['slug' => $proj->slug])?>">
          <h4 class="text-3xl"><?=$proj->name?></h4>
        </a>

        <p class="my-2">
          <?php if(\holonet\hgit\views\helpers\ViewFunctions::can($proj, \holonet\hgit\models\enum\Permission::READCODE, $this->user())): ?>
            <a class="mt-1" href="<?=$this->linkTo('webgit_show', ['slug' => $proj->slug, 'repository' => "{$proj->slug}.git"])?>">Git Repository</a>
          <?php endif; ?>
        </p>

        <p class="text-gray-500 my-2">Maintainer: <?=$proj->maintainer->get()->username?></p>
      </div>
      <hr>
    <?php endforeach; ?>
  <?php else: ?>
    There seems to be nothing here :(
  <?php endif; ?>
<?=$this->endBlock()?>
<?=$this->block('sidebar')?>
  <h4 class="text-xl">Quick Links</h4>
  <br>
  <?php if (!$this->guest()): ?>
    <a href="<?=$this->linkTo('projects_new')?>">Create a new project</a>
  <?php endif; ?>
<?=$this->endBlock()?>
<?=$this->block('top')?>
<?=$this->endBlock()?>
