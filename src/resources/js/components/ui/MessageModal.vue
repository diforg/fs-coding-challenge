<script setup>
import { reactive, ref } from "vue";
import { useGlobalStore } from "@/stores/global";
import { Inertia } from "@inertiajs/inertia";
import ChannelsBadge from "@/components/ui/ChannelsBadge.vue";

const emit = defineEmits(["close"]);

const props = defineProps({
  channels: Array,
  modalContacts: Array,
});

const store = useGlobalStore();

const isOpen = ref(true);
const loading = ref(false);

const formData = reactive({
  channel: "",
  message: "",
  contact_id: "",
});

const contacts = ref([]);

function changeChannel(channel,) {
  if (loading.value) return;

  formData.channel = channel;

  Inertia.get("/", store.mergeQueryParams({ modalChannel: channel }), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      contacts.value = props.modalContacts;
    },
    onError: ({ error }) => {
      alert(error);
    },
  });
}

function closeModal() {
  contacts.value = [];
  emit("close");
}

async function sendMessage() {
  loading.value = true;

  if (!formData.channel) {
    loading.value = false;
    return alert("Selecione o canal");
  }

  if (!formData.contact_id) {
    loading.value = false;
    return alert("Selecione o contato");
  }

  if (!formData.message.trim()) {
    loading.value = false;
    return alert("Preencha a mensagem");
  }

  await store.sendMessage(formData.message, formData.contact_id, () => {});

  formData.message = "";
  closeModal();

  await store.changeChannel(formData.channel, () => {});
  await store.changeContact(formData.contact_id);
  loading.value = false;
}
</script>

<template>
  <transition name="fade">
    <div
      v-if="isOpen"
      class="message-modal fixed inset-0 flex items-center justify-center z-50"
    >
      <div
        class="message-modal__container bg-white rounded-lg shadow-lg w-11/12 max-w-md p-6 relative"
      >
        <h2 class="text-xl font-bold mb-4" style="color: var(--border-color)">
          Nova mensagem
        </h2>
        <p style="color: var(--secondary-text-color); margin-bottom: 0.5rem">
          Selecione o canal de contato abaixo
        </p>

        <div v-if="loading" class="spinner"></div>
        <div class="message-modal__channels">
          <ChannelsBadge
            :channel="channel"
            v-for="channel in channels"
            :key="channel"
            :selected="formData.channel === channel"
            :title="`Veja suas mensagens do ${channel}!`"
            :disabled="loading"
            @click="changeChannel(channel)"
          />
        </div>
        <select
          :disabled="loading"
          name=""
          id=""
          class="message-modal__contacts"
          v-model="formData.contact_id"
        >
          <option value="" selected disabled>Selecione um contato</option>
          <option :value="contact.id" v-for="contact in contacts">
            {{ contact.name }}
          </option>
        </select>

        <textarea
          :disabled="loading"
          name=""
          class="message-modal__message"
          id=""
          rows="4"
          placeholder="Escreva sua mensagem"
          v-model="formData.message"
        />

        <!-- Ações -->
        <div style="gap: 1rem" class="flex justify-end space-x-2">
          <button
            @click="closeModal"
            style="
              border: 1px solid var(--secondary-text-color);
              background-color: var(--white);
              color: var(--secondary-text-color);
            "
            class="message-modal__button px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
            :disabled="loading"
          >
            Cancelar
          </button>
          <button
            @click="sendMessage()"
            :disabled="loading"
            class="message-modal__button"
            style="background-color: var(--border-color)"
          >
            Confirmar
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
@reference "tailwindcss";

.message-modal {
  background: var(--shadow-modal-color);
}

.message-modal__button:disabled {
  background-color: var(--disabled-color) !important;
  border: 0 !important;
  color: var(--white) !important;
}

.message-modal__contacts {
  @apply block mt-4 mb-4 h-[35px] rounded text-sm px-2 w-[450px];
  color: var(--secondary-text-color);
  border: 1px solid var(--disabled-color);
  outline: 0;
}

.message-modal__contacts:disabled {
  background-color: var(--disabled-color);
}

.message-modal__contacts:focus-visible {
  border-color: var(--border-color);
}

.message-modal__message {
  @apply mb-6 rounded px-2 w-[450px];
  color: var(--secondary-text-color);
  font-size: 15px;
  border: 1px solid var(--disabled-color);
  outline: none;
}

.message-modal__message:disabled {
  background-color: var(--disabled-color);
}

.message-modal__message:focus-visible {
  border-color: var(--border-color);
}

.message-modal__channels {
  @apply flex gap-4 px-[17px] pt-[10px] pb-0 rounded-[10px];
}

.message-modal__channels--disabled {
  background-color: var(--disabled-color);
}

.message-modal__container {
  @apply flex flex-col items-center w-full p-5 max-w-[600px];
  background: var(--modal-background-color);
}

.spinner {
  border: 8px solid var(--primary-gray-color);
  border-top: 8px solid var(--border-color);
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
