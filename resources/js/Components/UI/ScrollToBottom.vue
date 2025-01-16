<template>
    <transition name="scroll-btn">
        <button
            v-if="isVisible"
            @click="scrollToBottom"
            class="sticky bottom-4 left-10 transform -translate-x-1/2 p-2 bg-[#111827] text-white rounded-full shadow-lg hover:bg-[#232b3d] transition-colors"
            aria-label="Scroll to bottom"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="lucide lucide-arrow-down"
            >
                <path d="M12 5v14" />
                <path d="m19 12-7 7-7-7" />
            </svg>
        </button>
    </transition>
</template>

<script setup>
defineProps({
    isVisible: {
        type: Boolean,
        required: true,
    },
    scrollToBottom: {
        type: Function,
        required: true,
    },
});
</script>

<style scoped>
.scroll-btn-enter-active,
.scroll-btn-leave-active {
    transition: all 0.3s ease;
}

.scroll-btn-enter-from,
.scroll-btn-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
