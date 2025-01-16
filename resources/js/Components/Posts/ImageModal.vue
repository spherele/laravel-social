<script setup>
import { onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    isImageModalOpen: {
        type: Boolean,
        required: true,
    },
    selectedImage: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['close']);

const closeImageModal = () => {
    emit('close');
};

const disableScroll = () => {
    document.body.style.overflow = 'hidden';
};

const enableScroll = () => {
    document.body.style.overflow = '';
};

onMounted(() => {
    if (props.isImageModalOpen) {
        disableScroll();
    }
});

watch(
    () => props.isImageModalOpen,
    (isOpen) => {
        if (isOpen) {
            disableScroll();
        } else {
            enableScroll();
        }
    }
);

onUnmounted(() => {
    enableScroll();
});
</script>

<template>
    <transition name="fade">
        <div
            v-if="isImageModalOpen"
            class="fixed inset-0 z-50 flex justify-center items-center bg-black/80"
            @click="closeImageModal"
            style="margin-top: 0 !important"
        >
            <div class="relative max-w-[90vw] max-h-[90vh]">

                <button
                    @click="closeModal"
                    class="absolute -top-4 -right-4 text-gray-600 hover:text-gray-900 bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg"
                    style="font-size: 24px"
                >
                    &times;
                </button>

                <img
                    v-if="selectedImage"
                    :src="selectedImage"
                    alt=""
                    class="max-w-[80vw] max-h-[80vh] object-contain rounded"
                    style="border-radius: 14px"
                />
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
