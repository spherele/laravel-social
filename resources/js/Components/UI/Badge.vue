<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
    },
    class: {
        type: String,
        default: '',
    },
});

const baseClasses = 'inline-flex items-center px-2 py-1 rounded-full text-sm font-medium';

const variants = {
    default: 'bg-gray-100 text-gray-800',
    secondary: 'bg-gray-200 text-gray-600',
    success: 'bg-green-100 text-green-800',
    warning: 'bg-yellow-100 text-yellow-800',
    danger: 'bg-red-100 text-red-800',
};

const variantClasses = computed(() => {
    return variants[props.variant] || variants.default;
});
</script>

<template>
  <span :class="[baseClasses, variantClasses, props.class]">
    <slot />
  </span>
</template>
