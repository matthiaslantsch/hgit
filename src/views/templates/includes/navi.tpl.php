<nav class="bg-gray-900 border-b border-gray-700 border-opacity-60 sticky top-0 z-50">
  <div class="mx-auto px-6 py-4 flex items-center justify-between gap-6">
    <div class="flex items-center">
      <a href="<?=$this->linkTo('homepage')?>" title="Homepage" class="
        px-2 py-1 transition hover:opacity-75 focus:outline-none
        text-gray-400 hover:text-gray-300 pr-4"
      >
        <img src="<?=$this->linkAsset('favicon.ico')?>" alt="Hgit" class="h-9 w-auto rounded-lg">
      </a>
      <div class="flex items-center gap-1">
<!--        <a href="#" class="-->
<!--          inline-flex gap-2.5 items-center rounded-full whitespace-nowrap px-4 py-2 font-medium text-sm-->
<!--          xl:text-base text-gray-300 bg-gray-700"-->
<!--        >-->
<!--          <svg class="mt-0.25 w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"-->
<!--            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">-->
<!---->
<!--            <path stroke-linecap="round" stroke-linejoin="round" d="-->
<!--              M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867-->
<!--              6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967-->
<!--              8.967 0 0 0-6 2.292m0-14.25v14.25"-->
<!--            />-->
<!--          </svg>-->
<!---->
<!--          <span>Documentation</span>-->
<!--        </a>-->
      </div>
    </div>

    <div class="flex items-center justify-end gap-2">
      <h1 class="text-2xl text-gray-400"><?=$title?></h1>
      <div class="relative hidden md:block">
        <label for="search" class="sr-only">Search</label>

        <span style="position: relative; display: inline-block; direction: ltr;">
          <input x-data id="search"
            class="border-gray-900 block w-full rounded-md border bg-gray-800 pl-10 pr-4 py-2.5 text-sm text-gray-300
            placeholder-gray-600 outline-none transition-color focus:border-gray-900 focus:ring-1"
            type="text" placeholder="Search" autocomplete="off" spellcheck="false" dir="auto"
            @keyup.escape="$event.target.value = ''; $event.target.blur()"
          >
        <div class="pointer-events-none absolute inset-0 left-3 flex items-center" aria-hidden="true">
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="
              m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1
              1-18 0 9 9 0 0 1 18 0Z"
            />
          </svg>
        </div>
        </span>
      </div>
      <?php if ($this->guest()): ?>
        <a href="<?=$this->linkTo('login')?>" class="
          inline-flex gap-2.5 items-center rounded-full whitespace-nowrap px-4 py-2 font-medium text-sm
          xl:text-base text-gray-300 bg-gray-700"
        >
          <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="
              M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25
              2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"
            />
          </svg>
          Login
        </a>
      <?php else: ?>
        <div x-init x-data="{ open: false }">
          <button @click="open = !open" class="rounded-md p-1 text-gray-400 font-medium text-sm xl:text-base text-gray-300 text-gray-400 hover:text-gray-300">
            <img src="https://www.gravatar.com/avatar/<?=md5($this->user('email') ?? $this->user('username'))?>>?s=80" alt="<?=$this->user('email')?>" class="w-8 h-8 rounded-full">
          </button>
          <div
            x-show="open"
            x-transition.origin.top.left
            @click.outside="open = false"
            style="display: none"
            class="absolute right-0 mt-2 w-40 rounded-md bg-gray-900 border-gray-800 shadow-md border"
          >
            <a href="#" class="
              flex items-center gap-2 w-full px-4 py-2.5 text-left text-sm text-gray-400 hover:text-gray-300"
            >
              <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
              >

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="" />
                </svg>

                <path stroke-linecap="round" stroke-linejoin="round" d="
                  M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963
                  0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                />
              </svg>
              <?=$this->user('name')?>
            </a>

            <a href="#" class="
              flex items-center gap-2 w-full px-4 py-2.5 text-left text-sm text-gray-400 hover:text-gray-300"
            >
              <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
              >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="" />
                </svg>


                <path stroke-linecap="round" stroke-linejoin="round" d="
                  M6 13.5V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m12-3V3.75m0 9.75a1.5 1.5 0 0 1
                  0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m-6-9V3.75m0 3.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0
                  9.75V10.5"
                />
              </svg>
              Settings
            </a>

            <a href="<?=$this->linkTo('tokens_index')?>" class="
              flex items-center gap-2 w-full px-4 py-2.5 text-left text-sm text-gray-400 hover:text-gray-300"
            >
              <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="
                  M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824
                  10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196
                  0-6.1-1.248-8.25-3.285Z"
                />
              </svg>
              Tokens
            </a>

            <hr>

            <a href="<?=$this->linkTo('logout')?>" class="
              flex items-center gap-2 w-full px-4 py-2.5 text-left text-sm text-gray-400 hover:text-gray-300"
            >
              <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25
                  0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15"
                />
              </svg>
              Logout
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>
