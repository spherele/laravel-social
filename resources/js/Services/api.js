import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;
const API_AUTH_URL = import.meta.env.VITE_AUTH_URL;

const apiClient = axios.create({
    baseURL: API_BASE_URL,
});

const authClient = axios.create({
    baseURL: API_AUTH_URL,
});

apiClient.interceptors.request.use((config) => {
    const token = localStorage.getItem('access_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

apiClient.interceptors.response.use(
    (response) => response,
    async (error) => {
        console.error('There was a problem with the fetch operation:', error);
        return Promise.reject(error);
    }
);

authClient.interceptors.request.use((config) => {
    const token = localStorage.getItem('access_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

authClient.interceptors.response.use(
    (response) => response,
    (error) => {
        console.error('There was a problem with the auth request:', error);
        return Promise.reject(error);
    }
);

export const fetchPosts = async () => {
    const cacheKey = 'posts_data';
    const cacheTimestampKey = 'posts_data_timestamp';
    const cacheDuration = 10 * 60 * 1000;

    const cachedData = localStorage.getItem(cacheKey);
    const cachedTimestamp = localStorage.getItem(cacheTimestampKey);

    if (cachedData && cachedTimestamp && Date.now() - cachedTimestamp < cacheDuration) {
        return JSON.parse(cachedData);
    }

    try {
        const response = await apiClient.get('/posts');

        localStorage.setItem(cacheKey, JSON.stringify(response.data));
        localStorage.setItem(cacheTimestampKey, Date.now());

        return response.data;
    } catch (error) {
        throw error;
    }
};

export const createPost = async (postData) => {
    try {
        const response = await apiClient.post('/posts', postData);

        localStorage.removeItem('posts_data');
        localStorage.removeItem('posts_data_timestamp');

        return response.data;
    } catch (error) {
        throw error;
    }
};

export const deletePost = async (postId) => {
    try {
        const response = await apiClient.delete(`/posts/${postId}`);

        localStorage.removeItem('posts_data');
        localStorage.removeItem('posts_data_timestamp');

        return response.data;
    } catch (error) {
        throw error;
    }
};
