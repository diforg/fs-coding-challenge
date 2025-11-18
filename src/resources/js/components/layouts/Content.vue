<script setup>
import { useGlobalStore } from "@/stores/global";
import { Icon } from "@iconify/vue";
import Chat from "../layouts/Chat.vue";
import ThemeToggle from "../ui/ThemeToggle.vue";

const store = useGlobalStore();

function closeChat() {
  store.changeContact(false);
}
</script>

<template>
  <div class="content">
    <ThemeToggle />
    <div title="Fechar" @click="closeChat">
      <Icon
        v-if="store.selectedContact"
        class="content__close"
        :icon="'carbon:close-outline'"
        width="34"
        height="34"
        color="#df4d4d"
      />
    </div>
    <div v-if="store.selectedContact" class="content__body" @keyup.esc="closeChat">
      <Chat />
    </div>
    <div class="content__empty" v-else>
      <img :src="`/images/logo-${store.theme}.svg`" alt="" />
    </div>
  </div>
</template>

<style scoped>
@reference "tailwindcss";

.content {
  @apply w-full relative;
  background: var(--content-background-color);
}

.content__body {
  @apply h-full;
}

.content__close {
  @apply absolute cursor-pointer;
  right: 100px;
  top: 24px;
}

.content__empty {
  @apply h-full flex items-center justify-center;
}

.content input:focus {
  border-color: var(--border-color);
  outline: none;
}

</style>
