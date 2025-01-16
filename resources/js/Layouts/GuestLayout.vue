<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 super-center-items">
        <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg one-more"
        >
            <slot />
        </div>
    </div>
</template>
<style scoped>
    .super-center-items{
        display: flex;
        justify-content: center;
        @media(max-width: 768px) {
            padding: 0 10px;
        }
    }

    .one-more {
        @media (max-width: 768px) {
            border-radius: 14px;
        }
    }
</style>
