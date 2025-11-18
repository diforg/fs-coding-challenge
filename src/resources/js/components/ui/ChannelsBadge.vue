<script setup>
import { Icon } from "@iconify/vue";
import { useGlobalStore } from "@/stores/global";
import { reactive } from "vue";

const props = defineProps({
  channel: String,
  selected: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  small: {
    type: Boolean,
    default: false,
  },
  onlyIcon: {
    type: Boolean,
    default: false,
  },
  onlytext: {
    type: Boolean,
    default: false,
  },
});

const store = useGlobalStore();

const bind = reactive({
  icon: store.getChannelIcon(props.channel),
  width: props.small ? "16" : "20",
  height: props.small ? "16" : "20",
  color: props.selected ? "0c6f64" : "556377",
});

function capitalizeFirstLetter(str) {
  if (str === "all") return "Todas";

  if (!str) return "";
  return str.charAt(0).toUpperCase() + str.slice(1);
}
</script>

<template>
  <div
    class="channels-badge"
    :class="{
      'channels-badge--small': small,
      'channels-badge--selected': selected,
      'channels-badge--disabled': disabled,
      'channels-badge--dark': store.theme === 'dark',
    }"
  >
    <Icon v-show="channel !== 'all'" v-bind="bind" />
    <span v-show="!onlyIcon">{{ capitalizeFirstLetter(channel) }}</span>
  </div>
</template>

<style scoped>
@reference "tailwindcss";

.channels-badge {
  @apply flex items-center gap-2 px-3 py-2 cursor-pointer rounded-full border font-normal min-w-fit;
  border-color: var(--channel-badge-border-color);
}

.channels-badge span {
  @apply text-sm;
  color: var(--channel-badge-text-color);
}

.channels-badge--small {
  @apply gap-1 px-1.5 py-1;
}

.channels-badge--small span {
  @apply text-xs;
}

.channels-badge--selected {
  @apply border;
  background: var(--selected-background-color);
  border-color: var(--border-color);
}

.channels-badge--selected span {
  color: var(--selected-channel-color);
}

.channels-badge--selected svg {
  color: var(--selected-channel-color) !important;
}

.channels-badge--disabled {
  background-color: var(--disabled-color) !important;
  border: 0;
}
</style>
