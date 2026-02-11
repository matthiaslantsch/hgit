<?=$this->extends('includes/layout-full')?>
<?=$this->block('main')?>
  <h1 class="text-xl">Create a new project</h1>
  <form x-init x-data="{ slug: '' }" action="<?=(isset($model) ? $this->linkREST($model, 'update') : $this->linkTo('projects_create'))?>" autocomplete="on" method="POST" class="
    w-full rounded-3xl p-8 bg-gray-800 border-2 border-gray-700 xl:p-10 mt-32
    ">
    <h1 class="text-3xl"><?=$title?></h1>
    <?php if(isset($model)): ?>
      <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>
    <?php if (!isset($model)): ?>
      <div class="flex gap-x-2 mx-4">
        <div class="w-1/2 py-4">
          <label class="text-white text-bold block">
            Project Name
            <input name="<?=$formKey?>[name]" type="text" minlength="2" maxlength="40" @keyup.debounce="slug = String($event.target.value)
              .normalize('NFKD') // split accented characters into their base characters and diacritical marks
              .replace(/[\u0300-\u036f]/g, '') // remove all the accents, which happen to be all in the \u03xx UNICODE block.
              .trim() // trim leading or trailing whitespace
              .toLowerCase() // convert to lowercase
              .replace(/[/]/g, '-') // replace slashes with hyphens
              .replace(/[^a-z0-9 -]/g, '') // remove non-alphanumeric characters
              .replace(/\s+/g, '-') // replace spaces with hyphens
              .replace(/-+/g, '-'); // remove consecutive hyphens
            " value="<?=($data?->name)?>" class="
            <?=(isset($errors['name']) ? 'border-red-500' : '')?>
            shadow appearance-none border border-gray-700 border-opacity-80 shadow-sm focus:outline-none focus:ring-blue-500
            focus:border-blue-500 rounded py-2 px-3 text-white bg-gray-900 w-full text-white mt-2" required>

            <?php foreach ($errors['name'] ?? array() as $error): ?>
              <p class="text-red-500 text-xs italic"><?=$error?></p>
            <?php endforeach; ?>
          </label>
        </div>
        <div class="w-1/2 py-4">
          <label class="text-white text-bold block">
            Slug
            <input type="text" disabled readonly id="project-slug" :value="slug" class="
              shadow appearance-none border border-gray-700 border-opacity-80 shadow-sm focus:outline-none focus:ring-blue-500
              focus:border-blue-500 rounded py-2 px-3 text-white bg-gray-900 w-full text-white mt-2" required value="<?=$data?->slug?>">
          </label>
        </div>
      </div>
    <?php endif; ?>
    <label class="text-white text-bold block mx-4">
      Description
      <textarea name="<?=$formKey?>[description]" class="
        shadow appearance-none border border-gray-700 border-opacity-80 shadow-sm focus:outline-none
        focus:ring-blue-500 focus:border-blue-500 rounded py-2 px-3 text-white bg-gray-900 w-full text-white"
      ><?=$data?->description ?? $model?->description ?? ''?></textarea>
    </label>
    <?php if (!isset($model)): ?>
      <div class="flex flex-col gap-y-2 p-4">
        <h1 class="text-white text-bold block mb-2">Permission Preset</h1>
        <?php foreach (\holonet\hgit\models\enum\PermissionPreset::cases() as $preset): ?>
        <label class="p-4 rounded-sm border-gray-700 border-opacity-80 bg-gray-900 w-full block select-none cursor-pointer hover:opacity-80">
          <input name="<?=$formKey?>[permissionPreset]" type="radio" value="<?=$preset->value?>" <?=($data?->preset() === $preset) ? 'checked' : ''?> required>
          <?=$this->includeStatic("icons/{$preset->value}-project.svg")?>

          <?=ucfirst($preset->value)?>
          <br>
          <small class="text-grey-200">
            <?=$preset->description()?>
          </small>
        </label>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div class="p-4 flex flex-col gap-y-2">
      <h1 class="text-white text-bold block">Project Type</h1>
      <?php foreach (\holonet\hgit\models\enum\ProjectType::cases() as $type): ?>
        <label class="p-4 rounded-sm border-gray-700 border-opacity-80 bg-gray-900 w-full block select-none cursor-pointer hover:opacity-80">
          <input name="<?=$formKey?>[type]" type="radio" value="<?=$type->value?>" <?=(($data?->type ?? $model?->type ?? null) === $type ? 'checked' : '')?> required>
          <?=$type->value?>
          <br>
        </label>
      <?php endforeach; ?>
    </div>
    <div class="m-4">
      <button class="w-full rounded border border-gray-700 p-4 border-opacity-80 text-white bg-gray-900 hover:opacity-80" type="submit">Submit</button>
    </div>
  </form>
<?=$this->endBlock()?>
