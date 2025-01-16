<script setup>
const props = defineProps({
    class: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div
        :class="[
            'rounded-lg shadow bg-white',
            props.class
        ]"
    >
        <slot />
    </div>
</template>
