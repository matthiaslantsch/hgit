<div x-init="() => { flashShown = true; setTimeout(() => { flashShown = false }, 1000) };"
     x-data="{ flashShown: false }"
     x-show="flashShown"
     x-cloak
     @click="flashShown = false"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-out duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed mt-100 left-1/3 top-1/3 w-1/4 p-4 rounded <?=$class?> z-100 cursor-pointer">
  <?=$message?>
</div>
