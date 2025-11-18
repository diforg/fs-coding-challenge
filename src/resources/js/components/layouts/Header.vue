<script setup>
import { useGlobalStore } from "@/stores/global";
import { Icon } from "@iconify/vue";
const store = useGlobalStore();
</script>

<template>
  <div class="header">
    <div class="header__logo">
      <img :src="`/images/logo-${store.theme}.svg`" alt="" />
    </div>

    <div class="header__new-message">
      <Icon
        :icon="'lucide:plus-circle'"
        width="22"
        height="22"
        :color="store.theme === 'light' ? '#2B313B' : '#fff'"
        @click="store.toggleNewMessageModal(true)"
      />
    </div>
  </div>
</template>

<style scoped>
@reference "tailwindcss";

.header {
  @apply flex items-center justify-between w-full p-8;
}
.header__logo {
  @apply w-[230px];
}
.header__logo img {
  @apply w-full h-full;
}
.header__new-message {
  @apply cursor-pointer;
}
</style>
