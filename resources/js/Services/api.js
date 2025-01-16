import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

const apiClient = axios.create({
    baseURL: API_BASE_URL,
});

apiClient.interceptors.response.use(
    (response) => {
        if (response.data) {
            return { data: response.data };
        }
        return response;
    },
    (error) => {
        console.error('There was a problem with the fetch operation:', error);
        return Promise.reject(error);
    }
);

export const fetchPosts = async () => {
    try {
        const response = await apiClient.get('/posts');
        return response.data;
    } catch (error) {
        throw error;
    }
};

export const createPost = async (postData) => {
    try {
        const response = await apiClient.post('/posts', postData);
        return response.data;
    } catch (error) {
        throw error;
    }
};
