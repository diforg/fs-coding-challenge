<script setup>
import { Head } from "@inertiajs/inertia-vue3";
import Header from "@/components/layouts/Header.vue";
import Sidebar from "@/components/layouts/Sidebar.vue";
import Container from "@/components/layouts/Container.vue";
import Channels from "@/components/layouts/Channels.vue";
import Contacts from "@/components/layouts/Contacts.vue";
import Content from "@/components/layouts/Content.vue";
import MessageModal from "@/components/ui/MessageModal.vue";

import { useGlobalStore } from "@/stores/global";
import { onMounted } from "vue";

const props = defineProps({
  contacts: Array,
  channels: Array,
  modalContacts: Array,
});

const store = useGlobalStore();

onMounted(async () => {
  const params = new URLSearchParams(window.location.search);
  const channel = params.get("channel") || "all";

  await store.changeChannel(channel);
});
</script>

<template>
  <div @keyup.esc="store.changeContact(false);">
    <Head>
      <title>{{ "Chat - Coding Challenge" }}</title>
      <link rel="icon" href="/images/logo.png" />
    </Head>
    <Container>
      <Sidebar>
        <Header />
        <Channels :channels="channels" @openModal="store.toggleNewMessageModal(true)" />
        <Contacts :contacts="contacts" />
      </Sidebar>
      <Content />
      <MessageModal
        v-if="store.newMessageModal"
        :channels="channels"
        @close="store.toggleNewMessageModal(false)"
        :modalContacts="modalContacts"
      />
    </Container>
  </div>
</template>
