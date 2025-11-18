import { defineStore } from "pinia";
import { Inertia } from '@inertiajs/inertia';
import { nextTick } from "vue";

export const useGlobalStore = defineStore("global", {
  state: () => ({
    selectedChannel: 'all',
    newMessageModal: false,
    selectedContact: 0,
    selectedContactName: '',
    selectedContactPhoto: '',
    theme: localStorage.getItem("theme") || "light",
    messages: [],
    pollingInterval: null,
    messagesPage: 1,
    hasMoreMessages: true,
  }),
  actions: {
    changeChannel(selected) {
      this.selectedChannel = selected;
      return new Promise((resolve, reject) => {
        Inertia.get('/', this.mergeQueryParams({ channel: selected }, ['contact_id', 'page']), {
          preserveState: true,
          replace: true,
          onSuccess: () => resolve(),
          onError: (err) => reject(err),
        });
      });
    },
    toggleNewMessageModal(status) {
      this.newMessageModal = status;
    },
    changeContact(contactId, contactName, contactPhoto) {
      if (!contactId) {
        this.selectedContact = 0;
        this.selectedContactName = '';
        this.selectedContactPhoto = '';
        this.messages = [];
        this.stopPolling();
        return Promise.resolve();
      }

      if (contactId === this.selectedContact) {
        return Promise.resolve();
      }

      this.selectedContact = contactId;
      this.selectedContactName = contactName;
      this.selectedContactPhoto = contactPhoto;
      this.messagesPage = 1;
      this.messages = [];

      this.stopPolling();

      this.startPolling();

      return new Promise((resolve, reject) => {
        Inertia.post(
          '/read-message',
          { contact_id: contactId },
          {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
              this.fetchMessages(true, true).then(() => {
                nextTick(() => {
                  const chat = document.querySelector('.chat__content');
                  if (chat && this.messages.length > 0) {
                    chat.scrollTop = chat.scrollHeight;
                  }
                });
                resolve();
              }).catch(reject);
            },
            onError: (err) => reject(err),
          }
        );
      });
    },
    async fetchMessages(reset = false, forPolling = false, forScrollTop = false) {
      if (!this.selectedContact) {
        this.messages = [];
        return;
      }

      if (reset) {
        this.messagesPage = 1;
        this.hasMoreMessages = true;
        this.messages = [];
      }

      const pageToLoad = forPolling ? 1 : this.messagesPage;

      if (!this.hasMoreMessages && !forPolling) return;

      return new Promise((resolve) => {
        Inertia.get(
          '/',
          this.mergeQueryParams({ contact_id: this.selectedContact, page: pageToLoad }, ['modalChannel']),
          {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onSuccess: (page) => {
              const newMessages = page.props.messages.data.reverse();

              if (forPolling) {
                const existingIds = this.messages.map(m => m.id);
                const messagesToAdd = newMessages.filter(m => !existingIds.includes(m.id));
                this.messages = [...this.messages, ...messagesToAdd];
              }else if (forScrollTop) {
                if (newMessages.length === 0) {
                  this.hasMoreMessages = false;
                } else {
                  const existingIds = this.messages.map(m => m.id);
                  const uniqueMessages = newMessages.filter(m => !existingIds.includes(m.id));
                  this.messages = [...uniqueMessages, ...this.messages];
                  this.messagesPage++;
                }
              } else if (reset) {
                this.messages = [...newMessages];
                this.messagesPage = 2;
              }

              resolve();
            },
          }
        );
      });
    },
    startPolling() {
      this.stopPolling();
      this.pollingInterval = setInterval(() => {
        this.fetchMessages(false, true);
      }, 3000);
    },
    stopPolling() {
      if (this.pollingInterval) {
        clearInterval(this.pollingInterval);
        this.pollingInterval = null;
      }
    },
    sendMessage(message, contact_id = '') {
      if ((!this.selectedContact && !contact_id) || !message) return Promise.resolve();

      let contactId = this.selectedContact;
      if (contact_id) {
        contactId = contact_id;
      }

      return new Promise((resolve, reject) => {
        Inertia.post(
          "/send-message",
          { contact_id: contactId, message },
          {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
              this.fetchMessages(true, true).then(() => {
                nextTick(() => {
                  const chat = document.querySelector('.chat__content');
                  if (chat) chat.scrollTop = chat.scrollHeight;
                });
                resolve();
              }).catch(reject);
            },
            onError: (err) => reject(err),
          }
        );
      });
    },
    getChannelIcon(channel) {
      const components = {
        whatsapp: 'mdi:whatsapp',
        telegram: 'basil:telegram-outline',
        messenger: 'mingcute:messenger-line',
      };
      return components[channel];
    },
    setTheme(theme) {
      this.theme = theme;
      localStorage.setItem("theme", theme);
      document.documentElement.setAttribute("data-theme", theme);
    },
    toggleTheme() {
      this.setTheme(this.theme === "light" ? "dark" : "light");
    },
    mergeQueryParams(newParams = {}, removeParams = []) {
      const currentParams = Object.fromEntries(new URLSearchParams(window.location.search));

      if (Array.isArray(removeParams)) {
        removeParams.forEach(param => {
          if (currentParams.hasOwnProperty(param)) {
            delete currentParams[param];
          }
        });
      }

      return { ...currentParams, ...newParams };
    }
  },
});
