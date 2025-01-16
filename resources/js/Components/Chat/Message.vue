<template>
    <div
        class="flex"
        :class="{ 'justify-end': message.sender === 'me', 'justify-start': message.sender !== 'me' }"
    >
        <Avatar
            v-if="message.sender !== 'me'"
            class="w-8 h-8 mr-2"
        >
            <img :src="message.avatar" :alt="message.sender" class="object-cover"/>
        </Avatar>
        <div
            class="message-width p-3 rounded-lg"
            :class="{
                'bg-[#111827] text-white': message.sender === 'me',
                'bg-[#f0f0f0] text-gray-800': message.sender !== 'me',
            }"
        >
            <p v-if="message.text" style="word-break: break-all; white-space: pre-wrap;" v-html="highlightCode(message.text)"></p>
            <div v-if="message.file" class="mt-2">

                <img
                    v-if="isImage(message.file)"
                    :src="message.file.url"
                    :alt="message.file.name"
                    class="h-auto rounded-lg cursor-pointer picture-width"
                    @click="openImageModal(message.file.url)"
                />
                <video
                    v-else-if="isVideo(message.file)"
                    ref="videoPlayer"
                    controls
                    class="h-auto rounded-lg video-width"
                >
                    <source :src="message.file.url" :type="message.file.type" />
                    Your browser does not support the video tag.
                </video>

                <div v-else class="flex items-center">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-gray-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                        />
                    </svg>
                    <a
                        :href="message.file.url"
                        class="ml-2 text-blue-500 hover:underline"
                        @click="downloadFile(message.file)"
                    >
                        {{ message.file.name }}
                    </a>
                </div>
            </div>
            <div class="flex items-center justify-between mt-1">
                <span class="text-xs text-gray-400">
                    {{ message.time }}
                    <span v-if="message.isEdited" class="text-gray-400 ml-1">(edited)</span>
                </span>
                <span v-if="message.sender === 'me'" class="text-xs text-gray-400 ml-2">
                    <!-- Иконка статуса прочтения -->
                    <template v-if="message.isRead">
                        <!-- Две галочки (прочитано) -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-check"
                        >
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-check"
                        >
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                    </template>
                    <template v-else>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-check"
                        >
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                    </template>
                </span>
            </div>
            <div
                v-if="message.sender === 'me'"
                class="flex items-center space-x-2 mt-1"
            >
                <button
                    v-if="showCopyButton"
                    @click="copyCode"
                    :disabled="isCopying"
                    class="text-xs text-gray-400 hover:text-gray-600"
                >
                    {{ copyButtonText }}
                </button>
                <button
                    @click="editMessage(message)"
                    class="text-xs text-gray-400 hover:text-gray-600"
                >
                    Edit
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue';
import { onMounted } from 'vue';
import { ref } from 'vue';
import Avatar from '@/Components/UI/Avatar.vue';
import hljs from 'highlight.js';
import 'highlight.js/styles/github.css';

import Plyr from 'plyr';
import 'plyr/dist/plyr.css';

const props = defineProps({
    message: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['editMessage', 'openImageModal', 'deleteMessage']);

const isImage = (file) => {
    return file.type.startsWith('image/');
};

const isVideo = (file) => {
    return file.type.startsWith('video/');
};

const editMessage = (message) => {
    emit('editMessage', message);
};

const openImageModal = (imageUrl) => {
    emit('openImageModal', imageUrl);
};

const highlightCode = (text) => {
    const regex = /```([\s\S]*?)```/g;
    return text.replace(regex, (match, code) => {
        const highlighted = hljs.highlightAuto(code).value;
        return `<div style="background-color: #232323; padding: 12px; border-radius: 6px; overflow-x: auto; white-space: pre-wrap; word-break: break-all; margin: 8px 0;">
                  <code style="font-family: 'Courier New', monospace; font-size: 14px; color: #eceff3;">${highlighted}</code>
                </div>`;
    });
};

const showCopyButton = computed(() => {
    return /```([\s\S]*?)```/g.test(props.message.text);
});

const copyButtonText = ref('Copy');
const isCopying = ref(false);

const copyCode = () => {
    if (isCopying.value) return;
    isCopying.value = true;

    const codeMatch = props.message.text.match(/```([\s\S]*?)```/);
    if (codeMatch && codeMatch[1]) {
        const code = codeMatch[1].trim();
        fallbackCopyCode(code);
    } else {
        isCopying.value = false;
    }
};

const fallbackCopyCode = (text) => {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            copyButtonText.value = 'Copied!';
            setTimeout(() => {
                copyButtonText.value = 'Copy';
            }, 2000);
        } else {
            copyButtonText.value = 'Failed to copy';
            setTimeout(() => {
                copyButtonText.value = 'Copy';
            }, 2000);
        }
    } catch (err) {
        console.error('Fallback copy failed:', err);
        copyButtonText.value = 'Failed to copy';
        setTimeout(() => {
            copyButtonText.value = 'Copy';
        }, 2000);
    } finally {
        document.body.removeChild(textarea);
        isCopying.value = false;
    }
};

const downloadFile = (file) => {
    const link = document.createElement('a');
    link.href = file.url;
    link.download = file.name;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};



onMounted(() => {
    const videoPlayers = document.querySelectorAll('video');
    videoPlayers.forEach((player) => {
        new Plyr(player);
    });
});

</script>

<style scoped>
pre {
    background-color: #000409;
    padding: 12px;
    border-radius: 6px;
    overflow-x: auto;
    white-space: pre-wrap;
    word-break: break-all;
    margin: 8px 0;
}

.picture-width {
    aspect-ratio: 1;
    max-width: 400px;
    object-fit: cover;
    @media (max-width: 768px) {
        max-width: 100%;
    }
}

.message-width {
    max-width: 50%;
    @media (max-width: 768px) {
        max-width: 90%;
    }
}

.video-width {
    width: 100%;
    @media (max-width: 768px) {
        max-width: 100%;
    }
}
</style>
