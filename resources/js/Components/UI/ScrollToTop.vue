<template>
    <transition name="scroll-btn">
        <button
            v-if="isVisible"
            @click="scrollToTop"
            class="fixed bottom-8 right-8 p-3 bg-gray-800 text-white rounded-full shadow-lg hover:bg-[#1f2937] transition-all duration-300 ease-in-out"
            aria-label="Scroll to top"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                />
            </svg>
        </button>
    </transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';


const isVisible = ref(false);

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const checkScroll = () => {
    isVisible.value = window.scrollY > 300;
};

onMounted(() => {
    window.addEventListener('scroll', checkScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', checkScroll);
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
