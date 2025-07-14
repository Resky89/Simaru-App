<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;

trait ApiResourceOperations
{
    /**
     * Get resource list from API
     *
     * @param Request $request
     * @param string $endpoint API endpoint
     * @param string $resourceName Name of resource for response
     * @param string $viewName View to render
     * @param string $sortField Default sort field
     * @param array $extraParams Extra query parameters
     * @param array $sortMappings Custom sort mappings
     * @return \Illuminate\Http\Response
     */
    protected function getResourceList(Request $request, $endpoint, $resourceName, $viewName, $sortField = 'created_at', $extraParams = [], $sortMappings = [])
    {
        try {
            // Mendapatkan parameter kueri
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);
            $search = $request->query('search', '');
            $sortOrder = $request->query('sort', '');

            // Membangun parameter kueri dasar
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
            ];

            // Menambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Menerapkan opsi pengurutan
            $queryParams = array_merge(
                $queryParams,
                $this->applySorting($sortOrder, $sortField, $sortMappings)
            );

            // Tambahkan parameter tambahan
            $queryParams = array_merge($queryParams, $extraParams);

            // Mengambil data dari API
            $result = $this->apiService->request('GET', $endpoint, [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Memeriksa kesalahan API
            $apiError = $this->handleApiError($result, $request, $viewName, "Gagal mengambil data $resourceName");
            if ($apiError) {
                return $apiError;
            }

            // Memproses data
            $resources = $result['data'] ?? [];

            // Format pagination
            $pagination = $this->formatPagination($result['pagination'] ?? null);

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    $resourceName => $resources,
                    'pagination' => $pagination
                ]);
            }

            return view($viewName, [
                $resourceName => $resources,
                $resourceName.'_pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, $viewName, [
                $resourceName => [],
                $resourceName.'_pagination' => null
            ]);
        }
    }

    /**
     * Get single resource from API
     *
     * @param Request $request
     * @param string $endpoint API endpoint
     * @param string $resourceName Name of resource for response
     * @param string $viewName View to render
     * @return \Illuminate\Http\Response
     */
    protected function getResource(Request $request, $endpoint, $resourceName, $viewName)
    {
        try {
            // Mengambil data dari API
            $result = $this->apiService->request('GET', $endpoint);

            // Memeriksa kesalahan autentikasi
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Memeriksa kesalahan API
            $apiError = $this->handleApiError($result, $request, $viewName, "Gagal mengambil data $resourceName");
            if ($apiError) {
                return $apiError;
            }

            // Memproses data
            $resource = $result['data'] ?? [];

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    $resourceName => $resource
                ]);
            }

            return view($viewName, [
                $resourceName => $resource
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, $viewName, [
                $resourceName => []
            ]);
        }
    }

    /**
     * Store resource via API
     *
     * @param Request $request
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @param string $successMessage Success message
     * @param string $redirectRoute Route to redirect on success
     * @return \Illuminate\Http\Response
     */
    protected function storeResource(Request $request, $endpoint, $data, $successMessage, $redirectRoute = null)
    {
        try {
            // Mengirim data ke API
            $result = $this->apiService->request('POST', $endpoint, [
                'json' => $data
            ]);

            // Memeriksa kesalahan autentikasi
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menyimpan data';

                // Format error message
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->withInput()->withErrors($errorMessage);
            }

            // Response sukses
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            // Redirect dengan pesan sukses
            if ($redirectRoute) {
                return redirect()->route($redirectRoute)->with('success', $successMessage);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal memproses permintaan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->withErrors('Gagal memproses permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Update resource via API
     *
     * @param Request $request
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @param string $successMessage Success message
     * @param string $redirectRoute Route to redirect on success
     * @return \Illuminate\Http\Response
     */
    protected function updateResource(Request $request, $endpoint, $data, $successMessage, $redirectRoute = null)
    {
        try {
            // Mengirim data ke API
            $result = $this->apiService->request('PUT', $endpoint, [
                'json' => $data
            ]);

            // Memeriksa kesalahan autentikasi
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui data';

                // Format error message
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->withInput()->withErrors($errorMessage);
            }

            // Response sukses
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            // Redirect dengan pesan sukses
            if ($redirectRoute) {
                return redirect()->route($redirectRoute)->with('success', $successMessage);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal memproses permintaan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->withErrors('Gagal memproses permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Delete resource via API
     *
     * @param Request $request
     * @param string $endpoint API endpoint
     * @param string $successMessage Success message
     * @param string $redirectRoute Route to redirect on success
     * @return \Illuminate\Http\Response
     */
    protected function deleteResource(Request $request, $endpoint, $successMessage, $redirectRoute = null)
    {
        try {
            // Mengirim permintaan hapus ke API
            $result = $this->apiService->request('DELETE', $endpoint);

            // Memeriksa kesalahan autentikasi
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus data';

                $errorMessage = is_array($errorData) ? implode(', ', (array)$errorData) : $errorData;

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->withErrors($errorMessage);
            }

            // Response sukses
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }

            // Redirect dengan pesan sukses
            if ($redirectRoute) {
                return redirect()->route($redirectRoute)->with('success', $successMessage);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal memproses permintaan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors('Gagal memproses permintaan: ' . $e->getMessage());
        }
    }
}
