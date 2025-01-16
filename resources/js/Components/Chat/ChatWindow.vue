<template>
    <Card class="p-6 relative h-screen flex flex-col">
        <!-- Хедер -->
        <div class="flex items-center relative">
            <button @click="goBackToChatList" class="absolute mr-2 text-gray-600 hover:text-gray-900 min-w-16">
                &larr; Back
            </button>
            <div class="flex justify-center w-full flex-col items-center pb-2">
                <h2 class="text-xl font-semibold">{{ selectedChat.name }}</h2>
                <span class="flex items-center gap-2 text-sm text-gray-600">
                    <span>{{ selectedChat.isOnline ? 'online' : 'offline' }}</span>
                    <span
                        class="inline-block w-2 h-2 rounded-full"
                        :class="selectedChat.isOnline ? 'bg-green-500' : 'bg-red-500'"
                    ></span>
                </span>
            </div>
        </div>

        <!-- Контейнер сообщений -->
        <div
            ref="chatContainer"
            class="space-y-4 flex-1 overflow-y-auto pb-1.5 pr-1.5 relative"
            @scroll="handleScroll"
        >
            <Message
                v-for="message in messages"
                :key="message.id"
                :message="message"
                @editMessage="editMessage"
                @openImageModal="openImageModal"
            />
            <ScrollToBottom :isVisible="isScrolledUp" :scrollToBottom="scrollToBottom" />
        </div>

        <!-- Поле ввода сообщения -->
        <div class="mt-4">
            <div class="flex items-center flex-end needle-class" style="position: sticky; bottom: 0;">
                <label for="file-input" class="p-2 cursor-pointer bg-[#111827] text-white rounded-lg mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-paperclip">
                        <path d="M13.234 20.252 21 12.3"/>
                        <path
                            d="m16 6-8.414 8.586a2 2 0 0 0 0 2.828 2 2 0 0 0 2.828 0l8.414-8.586a4 4 0 0 0 0-5.656 4 4 0 0 0-5.656 0l-8.415 8.585a6 6 0 1 0 8.486 8.486"/>
                    </svg>
                </label>
                <input
                    id="file-input"
                    type="file"
                    class="hidden"
                    @change="handleFileUpload"
                />
                <textarea
                    v-model="newMessage"
                    @keydown.enter="handleTextareaKeydown"
                    @input="adjustTextareaHeight"
                    type="text"
                    placeholder="Type a message..."
                    class="w-full p-2 border-[#111827] rounded-lg"
                    style="outline: none !important; box-shadow: none !important; min-height: 42px !important; max-height: 120px; height: 42px !important;"
                    wrap="soft"
                />
                <button
                    @click="sendMessage"
                    class="ml-2 p-2 bg-[#111827] text-white rounded-lg hover:bg-[#232b3d] transition-colors"
                >
                    <template v-if="editingMessage">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-send-horizontal">
                            <path
                                d="M3.714 3.048a.498.498 0 0 0-.683.627l2.843 7.627a2 2 0 0 1 0 1.396l-2.842 7.627a.498.498 0 0 0 .682.627l18-8.5a.5.5 0 0 0 0-.904z"/>
                            <path d="M6 12h16"/>
                        </svg>
                    </template>
                    <template v-else>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-send-horizontal">
                            <path
                                d="M3.714 3.048a.498.498 0 0 0-.683.627l2.843 7.627a2 2 0 0 1 0 1.396l-2.842 7.627a.498.498 0 0 0 .682.627l18-8.5a.5.5 0 0 0 0-.904z"/>
                            <path d="M6 12h16"/>
                        </svg>
                    </template>
                </button>
            </div>
        </div>
    </Card>
</template>

<script setup>
import { defineProps, defineEmits, ref, onMounted, watch, nextTick } from 'vue';
import Card from '@/Components/UI/Card.vue';
import ScrollToBottom from '@/Components/UI/ScrollToBottom.vue';
import Message from '@/Components/Chat/Message.vue';

const props = defineProps({
    selectedChat: {
        type: Object,
        required: true,
    },
    messages: {
        type: Array,
        required: true,
    },
});

const handleTextareaKeydown = (event) => {
    if (event.key === 'Enter') {
        if (event.shiftKey) {

            event.preventDefault();
            newMessage.value += '\n';

            nextTick(() => {
                adjustTextareaHeight(event);
            });
        } else {
            event.preventDefault();
            sendMessage();
        }
    }
};

const adjustTextareaHeight = (event) => {
    const textarea = event.target;
    textarea.style.height = '42px';
    textarea.style.height = `${textarea.scrollHeight}px`;

    if (textarea.scrollHeight > 120) {
        textarea.style.height = '120px';
        textarea.style.overflowY = 'auto';
    } else {
        textarea.style.overflowY = 'hidden';
    }
};

const emit = defineEmits([
    'goBackToChatList',
    'sendMessage',
    'handleFileUpload',
    'editMessage',
    'openImageModal',
]);

const newMessage = ref('');
const editingMessage = ref(null);
const chatContainer = ref(null);
const isScrolledUp = ref(false);

const scrollToBottom = () => {
    if (chatContainer.value) {
        chatContainer.value.scrollTo({
            top: chatContainer.value.scrollHeight,
            behavior: 'smooth',
        });
    }
};

const handleScroll = () => {
    if (chatContainer.value) {
        const { scrollTop, scrollHeight, clientHeight } = chatContainer.value;
        isScrolledUp.value = scrollTop + clientHeight < scrollHeight - 200;
    }
};

watch(
    () => props.messages.length,
    () => {
        nextTick(() => {
            if (!isScrolledUp.value) {
                scrollToBottom();
            }
        });
    }
);

onMounted(() => {
    scrollToBottom();
});

const sendMessage = () => {
    emit('sendMessage', newMessage.value, editingMessage.value);
    newMessage.value = '';
    editingMessage.value = null;

    const textarea = document.querySelector('textarea');
    if (textarea) {
        textarea.style.height = '42px';
    }
};

const goBackToChatList = () => {
    emit('goBackToChatList');
};

const handleFileUpload = (event) => {
    emit('handleFileUpload', event);
};

const editMessage = (message) => {
    editingMessage.value = message;
    newMessage.value = message.text;

    nextTick(() => {
        const textarea = document.querySelector('textarea');
        if (textarea) {
            adjustTextareaHeight(textarea);
        }
    });
};

const openImageModal = (imageUrl) => {
    emit('openImageModal', imageUrl);
};

</script>

<style scoped>
::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}

::-webkit-scrollbar-track {
    background: rgba(213, 213, 213, 0.54);
    border-radius: 5px;
}

::-webkit-scrollbar-thumb {
    background: #1f2937;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: #1f2937;
}

textarea {
    resize: none;
    min-height: 42px;
    max-height: 120px;
    width: 100%;
    box-sizing: border-box;
}

.needle-class {
    display: flex;
    align-items: flex-end;
}

.p-6.relative.h-screen.flex.flex-col {
    height: 85vh;
    @media (max-width: 768px) {
        height: calc(100vh - 66px);
    }
}
</style>
