<script setup>
import { useGlobalStore } from "@/stores/global";
import { onMounted, ref } from "vue";
import { Icon } from "@iconify/vue";

const store = useGlobalStore();

const status = ref("");

function setTheme() {
  if (status.value) {
    return store.setTheme("dark");
  }

  return store.setTheme("light");
}

onMounted(() => {
  if (store.theme) {
    status.value = store.theme;
  }
});
</script>

<template>
  <div class="theme-toggle">
    <input
      type="checkbox"
      class="theme-toggle__checkbox"
      id="theme-toggle-check"
      v-model="status"
      :checked="status === 'dark'"
      @change="setTheme"
    />
    <label class="theme-toggle__label" for="theme-toggle-check">
      <Icon icon="ri:moon-fill" width="14" height="14" color="f1c40f" />
      <Icon icon="noto-v1:sun" width="14" height="14" color="var(--white)" />
      <div class="theme-toggle__ball" :class="{'theme-toggle__ball--dark' : store.theme === 'dark'}"></div>
    </label>
  </div>
</template>

<style scoped>
@reference "tailwindcss";

.theme-toggle {
  @apply absolute;
  right: 32px;
  top: 30px;
}

.theme-toggle__checkbox {
  @apply absolute opacity-0;
}

.theme-toggle__label {
  @apply flex items-center justify-between rounded-full cursor-pointer relative;
  background-color: var(--primary-text-color);
  padding: 5px;
  height: 22px;
  width: 42px;
  transform: scale(1.5);
}

.theme-toggle__ball {
  @apply absolute rounded-full;
  background-color: var(--white);
  top: 2px;
  left: 2px;
  height: 18px;
  width: 18px;
  transform: translateX(0px);
  transition: transform 0.2s linear;
}

.theme-toggle__ball--dark {
  background-color: #000;
}

.theme-toggle__checkbox:checked + .theme-toggle__label .theme-toggle__ball {
  transform: translateX(20px);
}
</style>
