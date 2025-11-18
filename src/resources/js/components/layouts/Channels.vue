<script setup>
import { useGlobalStore } from "@/stores/global";
import ChannelsBadge from "@/components/ui/ChannelsBadge.vue";

defineProps({
  channels: Array,
});

const store = useGlobalStore();
</script>

<template>
  <div class="channels">
    <div class="channels__container">
      <ChannelsBadge
        channel="all"
        :selected="store.selectedChannel === 'all'"
        title="Mensagens"
        @click="store.changeChannel('all')"
      />
      <ChannelsBadge
        :channel="channel"
        v-for="channel in channels"
        :key="channel"
        :selected="store.selectedChannel === channel"
        :title="`Veja as mensagens do ${channel}!`"
        @click="store.changeChannel(channel)"
      />
    </div>
  </div>
</template>

<style scoped>
@reference "tailwindcss";

.channels {
  @apply relative flex items-center justify-between mb-3 px-[28px] pb-[12px] rounded-xl overflow-x-auto overflow-y-hidden;
}

.channels__container {
  @apply flex gap-3 pr-[60px];
}

.channels__item {
  @apply flex items-center gap-3 px-4 py-2.5 cursor-pointer border-2 rounded-2xl min-w-fit;
  border-color: var(--primary-gray-color);
}

.channels__item span {
  @apply text-sm font-medium;
  color: var(--secondary-text-color);
}

.channels__item--selected {
  @apply border-2 shadow-sm;
  background: var(--selected-background-color);
  border-color: var(--border-color);
}

.channels__item--selected span {
  color: var(--selected-channel-color);
  font-weight: 600;
}

.channels::-webkit-scrollbar {
  height: 6px;
}

.channels::-webkit-scrollbar-track {
  background: var(--primary-gray-color);
  border-radius: 8px;
}

.channels::-webkit-scrollbar-thumb {
  background: var(--border-color);
  border-radius: 8px;
  width: 30px;
}
</style>