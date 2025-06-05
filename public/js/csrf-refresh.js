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

// Track if we're currently refreshing the token
let isRefreshingToken = false;
let refreshTokenPromise = null;

// Function to refresh the CSRF token
const refreshCsrfToken = async () => {
    // If we're already refreshing, return the existing promise
    if (isRefreshingToken) {
        return refreshTokenPromise;
    }
    
    isRefreshingToken = true;
    refreshTokenPromise = new Promise(async (resolve, reject) => {
        try {
            const response = await fetch('/csrf-token-refresh', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to refresh CSRF token');
            }
            
            const data = await response.json();
            
            if (!data.token) {
                throw new Error('No token returned from server');
            }
            
            // Update the CSRF token in the meta tag
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                metaToken.content = data.token;
            }
            
            // Update the token in any forms on the page
            document.querySelectorAll('input[name="_token"]').forEach(input => {
                input.value = data.token;
            });
            
            resolve(data.token);
        } catch (error) {
            console.error('Error refreshing CSRF token:', error);
            reject(error);
        } finally {
            isRefreshingToken = false;
        }
    });
    
    return refreshTokenPromise;
};

// Set up a response interceptor
axios.interceptors.response.use(
    response => response,
    async error => {
        // Check if the error is a CSRF token mismatch (419 status)
        if (error.response && error.response.status === 419) {
            const originalRequest = error.config;
            
            // Prevent infinite retry loops
            if (originalRequest._retry) {
                return Promise.reject(error);
            }
            
            originalRequest._retry = true;
            
            try {
                // Get a fresh token
                const token = await refreshCsrfToken();
                
                // Update the token in the headers for the retry
                originalRequest.headers['X-CSRF-TOKEN'] = token;
                
                // Retry the original request with the new token
                return axios(originalRequest);
            } catch (refreshError) {
                console.error('Failed to refresh token and retry request:', refreshError);
                return Promise.reject(error);
            }
        }
        
        return Promise.reject(error);
    }
);

// Refresh the token when the page loads
document.addEventListener('DOMContentLoaded', () => {
    refreshCsrfToken().catch(error => {
        console.error('Initial token refresh failed:', error);
    });
});

// Console message to confirm the script is loaded
console.log('CSRF token auto-refresh utility loaded');
