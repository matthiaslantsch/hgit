<?=$this->extends('includes/layout-full')?>
<?=$this->block('main')?>
  <h3 class="text-xl mb-2">Token successfully created!</h3>

  <p class="text-red-400">Copy it now!</p>

  <label class="block text-sm font-medium text-gray-300 mb-1">
    Token
    <div class="flex items-stretch" x-data>
      <input type="text" x-ref="token" readonly value="<?=$token?>" class="block w-full border border-gray-700 border-opacity-80 shadow-sm focus:outline-none focus:ring-blue-500
              focus:border-blue-500 rounded py-2 px-3 text-white bg-gray-900 w-full text-white mt-2" />
      <button type="button" class="inline-flex items-center px-3 py-2" @click="navigator.clipboard.writeText($refs.token.value)">Copy</button>
    </div>
  </label>

  <a href="<?=$this->linkTo('tokens_index')?>">Back</a>
<?=$this->endBlock()?>
