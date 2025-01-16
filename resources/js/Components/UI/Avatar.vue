<script setup>
const props = defineProps({
    class: {
        type: String,
        default: '',
    },
    editable: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['avatar-uploaded']);

const handleAvatarUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            emit('avatar-uploaded', reader.result);
        };
        reader.readAsDataURL(file);
    }
};
</script>

<template>
    <div :class="[
            'inline-block rounded-full bg-gray-200 overflow-hidden relative group',
            props.class
        ]"
    >
        <slot/>
        <div v-if="editable" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
            <label for="avatar-upload" class="cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </label>
        </div>
    </div>
    <input
        id="avatar-upload"
        type="file"
        class="hidden"
        @change="handleAvatarUpload"
        accept="image/*"
    />
</template>
