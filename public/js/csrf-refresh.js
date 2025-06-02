/**
 * CSRF Token Auto-Refresh Utility
 *
 * This script handles CSRF token expiration by:
 * 1. Intercepting axios responses to detect CSRF token mismatches
 * 2. Automatically updating the CSRF token from the response
 * 3. Retrying the original request with the new token
 */

// Create a reference to the original axios request method
const originalAxiosRequest = axios.request;

// Set up a request interceptor
axios.interceptors.request.use(function (config) {
    // Get the CSRF token from the meta tag
    let token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token.content;
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Set up a response interceptor
axios.interceptors.response.use(
    response => response,
    async error => {
        // Check if the error is a CSRF token mismatch (419 status)
        if (error.response && error.response.status === 419) {
            const originalRequest = error.config;

            // Check if we got a new token in the response
            if (error.response.data && error.response.data.csrf_token && error.response.data.csrf_expired) {
                // Update the CSRF token in the meta tag
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                if (metaToken) {
                    metaToken.content = error.response.data.csrf_token;
                }

                // Update the token in any forms on the page
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = error.response.data.csrf_token;
                });

                // Set the new token in the headers for the retry
                originalRequest.headers['X-CSRF-TOKEN'] = error.response.data.csrf_token;

                // Prevent infinite retry loops by tracking retries
                if (!originalRequest._retry) {
                    originalRequest._retry = true;

                    // Retry the original request with the new token
                    return axios(originalRequest);
                }
            }
        }

        return Promise.reject(error);
    }
);

// Console message to confirm the script is loaded
console.log('CSRF token auto-refresh utility loaded');
