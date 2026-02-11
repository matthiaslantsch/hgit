<?=$this->extends('includes/layout-full')?>
<?=$this->block('main')?>
  <h3 class="text-xl mb-2">User authentication tokens (git)</h3>

  <table class="min-w-full border border-gray-200 mb-6">
    <caption class="sr-only">tokens</caption>
    <thead class="bg-gray-700">
    <tr>
      <th class="text-left px-4 py-2 border-b">Name</th>
      <th class="text-left px-4 py-2 border-b">Expiry Date</th>
      <th class="px-4 py-2 border-b text-right"></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($result as $token): ?>
      <tr class="hover:bg-gray-600">
        <td class="px-4 py-2 border-b">
          <?=htmlspecialchars($token->name)?>
        </td>
        <td class="px-4 py-2 border-b">
          <?=$token->expiry->format('Y-m-d')?>
        </td>
        <td class="px-4 py-2 border-b text-right">
          <form method="POST" action="<?=$this->linkREST($token, 'delete')?>" class="inline">
            <input type="hidden" name="_method" value="DELETE">
            <button
              type="submit"
              class="text-sm text-red-600 hover:text-red-800"
              x-data
              @submit="confirm('Are you sure?') ? false : $event.preventDefault()"
            >
              Delete
            </button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>

    </tbody>
  </table>

  <form method="POST" action="<?=$this->linkTo('tokens_create')?>" class="flex items-end space-x-3">
    <div class="flex-1">
      <label class="block text-sm font-medium text-white mb-1">
        Name
        <input
          type="text"
          name="token[name]"
          required
          class="shadow appearance-none border border-gray-700 border-opacity-80 shadow-sm focus:outline-none focus:ring-blue-500
            focus:border-blue-500 rounded py-2 px-3 text-white bg-gray-900 w-full text-white mt-2"
        >
      </label>
    </div>

    <div>
      <label class="block text-sm font-medium text-white mb-1">
        Expiry
        <input
          type="date"
          name="token[expiry]"
          required
          class="shadow appearance-none border border-gray-700 border-opacity-80 shadow-sm focus:outline-none focus:ring-blue-500
            focus:border-blue-500 rounded py-2 px-3 text-white bg-gray-900 w-full text-white mt-2"
        >
      </label>
    </div>

    <button
      type="submit"
      class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700"
    >
      Create
    </button>
  </form>
<?=$this->endBlock()?>
