import './bootstrap';

// Add axios interceptor for handling token refresh
axios.interceptors.response.use(
    response => response,
    async error => {
        const originalRequest = error.config;

        // If error is 401 and we haven't tried to refresh token yet
        if (error.response.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            try {
                // Attempt token refresh
                const refreshResponse = await axios.post('/auth/refresh-token');

                if (refreshResponse.data.success) {
                    // Retry the original request after successful refresh
                    return axios(originalRequest);
                } else if (refreshResponse.data.redirect) {
                    // Token refresh failed, redirect to login
                    window.location.href = refreshResponse.data.redirect;
                    return Promise.reject(error);
                }
            } catch (refreshError) {
                // Refresh token request failed, redirect to login
                window.location.href = '/login';
                return Promise.reject(error);
            }
        }

        // If not a 401 error or retry failed, just reject
        return Promise.reject(error);
    }
);
