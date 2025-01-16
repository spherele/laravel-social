<template>
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-semibold mb-4">Create a New Post</h2>
        <form @submit.prevent="submitPost">
            <TextArea
                v-model="postContent"
                placeholder="What's on your mind?"
                :rows="4"
                :required="true"
            />

            <div class="mb-4">
                <div class="flex gap-2">
                    <PrimaryButton type="submit">
                        Post
                    </PrimaryButton>

                    <input
                        type="file"
                        ref="fileInput"
                        @change="handleFileUpload"
                        class="block w-full text-sm text-gray-500
                           file:inline-flex file:items-center file:px-4 file:py-2
                           file:bg-gray-800 file:border file:border-transparent
                           file:rounded-md file:font-semibold file:text-xs file:text-white
                           file:uppercase file:tracking-widest
                           hover:file:bg-gray-700 focus:file:bg-gray-700 active:file:bg-gray-900
                           focus:file:outline-none focus:file:ring-2 focus:file:ring-indigo-500
                           focus:file:ring-offset-2 file:transition file:ease-in-out file:duration-150"
                        accept="image/*, video/*"
                    />
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextArea from "@/Components/TextArea.vue";

const postContent = ref('');
const postFile = ref(null);

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        postFile.value = file;
    }
};

const uploadFile = async (file) => {
    const formData = new FormData();
    formData.append('file', file);

    try {
        const response = await axios.post('/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        return response.data;
    } catch (error) {
        console.error('Failed to upload file:', error);
        throw error;
    }
};

const emit = defineEmits(['post-created']);

const fileInput = ref(null);

const submitPost = async () => {
    try {
        let fileUrl = null;
        let fileType = null;

        if (postFile.value) {
            const fileResponse = await uploadFile(postFile.value);
            fileUrl = fileResponse.file_url;
            fileType = fileResponse.file_type;
        }

        const newPost = {
            content: postContent.value,
            file_type: fileType,
            file_url: fileUrl,
            date: new Date().toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }),
            likes: 0,
            comments: 0,
            watches: 0,
        };

        emit('post-created', newPost);

        console.log('New Post:', newPost);

        postContent.value = '';
        postFile.value = null;

        if (fileInput.value) {
            fileInput.value.value = '';
        }
    } catch (error) {
        console.error('Failed to create post:', error);
    }
};


</script>
