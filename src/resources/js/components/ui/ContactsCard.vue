<script setup>
import { useGlobalStore } from "@/stores/global";
import ChannelsBadge from "./ChannelsBadge.vue";

const store = useGlobalStore();

const props = defineProps({
  contact: {
    type: Object,
  },
});

function formateDate(param) {
  if (!param) return;
  const date = new Date(param);

  return `${date.toLocaleDateString("pt-BR")} ${date.toLocaleTimeString("pt-BR", {
    hour: "2-digit",
    minute: "2-digit",
  })}`;
}
</script>

<template>
  <div
    class="contacts-card"
    :class="{ 'contacts-card--selected': store.selectedContact === contact.id }"
    @click="store.changeContact(contact.id, contact.name, contact.photo)"
  >
    <div class="contacts-card__photo">
      <img :src="contact.photo" alt="" />
    </div>
    <div class="contacts-card__container">
      <div class="contacts-card__body">
        <div class="contacts-card__header">
          <div class="contacts-card__title">
            <h6>{{ contact.name }}&nbsp;&nbsp;</h6>
            <ChannelsBadge :channel="contact.channel.name" small onlyIcon />
          </div>
          <span class="contacts-card__date">
            {{ formateDate(contact.last_message?.created_at) }}
          </span>
        </div>
        <div class="contacts-card__content">
          <p class="contacts-card__last-message">
            {{ contact.last_message?.message }}
          </p>
        </div>
      </div>
      <div v-if="contact.unread_messages_count" class="contacts-card__unread-message">
        <span>{{ contact.unread_messages_count }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
@reference "tailwindcss";

.contacts-card {
  @apply flex gap-3 px-8 py-[10px];
}

.contacts-card p {
  color: var(--secondary-text-color);
}

.contacts-card:hover {
  background: var(--contact-hover-color);
  cursor: pointer;
}

.contacts-card--selected {
  background: var(--contact-hover-color) !important;
}

.contacts-card__container {
  @apply flex items-center justify-between w-full;
}

.contacts-card__title {
  @apply flex items-center;
}

.contacts-card__title h6 {
  color: var(--primary-text-color);
  font-size: 18px;
}

.contacts-card__body {
  @apply w-full;
}

.contacts-card__photo {
  @apply w-14 min-w-14 rounded-full overflow-hidden;
}

.contacts-card__content {
  @apply flex items-center;
}

.contacts-card__header {
  @apply flex items-center justify-between w-full;
}

.contacts-card__date {
  font-size: 12px;
  color: var(--secondary-text-color);
}

.contacts-card__last-message {
  @apply truncate max-w-[340px];
}

.contacts-card__unread-message {
  @apply flex items-center justify-center rounded-full w-5 h-5 font-bold;
  background: var(--unread-message-color);
  color: var(--white);
  font-size: 12px;
}

</style>
