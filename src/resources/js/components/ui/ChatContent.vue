<script setup>
import { useGlobalStore } from "@/stores/global";

const store = useGlobalStore();

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
  <div class="chat-content">
    <!-- <div class="chat-content__divider">Hoje</div> -->

    <div :key="message.message" v-for="message in store.messages">
      <div v-if="message.origin === 'received'" class="chat-content__row">
        <div class="chat-content__msg chat-content__received">
          <div class="chat-content__text">
            {{ message.message }}
          </div>
          <div class="chat-content__time">{{ formateDate(message.created_at) }}</div>
        </div>
      </div>
      <div
        v-else-if="message.origin === 'sent'"
        class="chat-content__row chat-content__sent"
      >
        <div
          class="chat-content__msg chat-content__sent"
          style="background: var(--third-color)"
        >
          <div class="chat-content__text">
            {{ message.message }}
          </div>
          <div class="chat-content__time">
            {{ formateDate(message.created_at) }}
            <span class="chat-content__checks">
              <span class="chat-content__check chat-content__read"></span
              ><span class="chat-content__check chat-content__read"></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@reference "tailwindcss";

.chat-content {
  @apply overflow-auto;
}

.chat-content__divider {
  @apply inline-block mx-auto px-3 py-1.5 rounded-full text-xs;
  background: var(--secondary-text-color);
  color: var(--white);
}

.chat-content__msg {
  @apply inline-flex flex-col gap-1 p-[8px_10px_6px] rounded-[14px] break-words whitespace-pre-wrap;
  max-width: min(72ch, 86%);
  position: relative;
  box-shadow: 0 1px 0 rgba(0,0,0,0.1);
}

.chat-content__msg .chat-content__text {
  font-size: 14.5px;
  line-height: 1.35;
}

.chat-content__time {
  @apply flex gap-[6px] self-end text-[11.5px];
  color: var(--third-text-color);
}

.chat-content__row {
  @apply flex w-full;
}

.chat-content__row.received {
  @apply justify-start;
}

.chat-content__row.chat-content__sent {
  @apply justify-end;
}

.chat-content__sent {
  border-top-right-radius: 4px;
  margin-bottom: .25rem;
}

.chat-content__checks {
  @apply inline-flex gap-0.5;
  transform: translateY(1px);
}

.chat-content__check {
  @apply inline-block;
  width: 10px;
  height: 2px;
  border-bottom: 2px solid currentColor;
  border-left: 2px solid transparent;
  transform: skewX(-30deg);
  opacity: 0.85;
}

.chat-content__check.chat-content__read {
  color: var(--check-message-color);
}

.chat-content__received {
  background: var(--primary-background-color);
  border-top-left-radius: 4px;
  margin-bottom: .5rem;
}

</style>
