@extends('layouts.app')

@section('title', 'Brands Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Brands Management</h1>
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
            data-toggle="modal" data-target="#addBrandModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Brand
        </button>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Brands Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">Brand List</h6>
                </div>
                <div class="col-md-4">
                    <form class="d-flex" action="{{ route('brands') }}" method="GET">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search brands..."
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="brandsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Brand Name</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $brand)
                            <tr>
                                <td>{{ $brand['brand_id'] }}</td>
                                <td>{{ $brand['brand_name'] }}</td>
                                <td>{{ isset($brand['created_at']) ? date('Y-m-d H:i:s', strtotime($brand['created_at'])) : 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-brand"
                                            data-brand-id="{{ $brand['brand_id'] }}"
                                            data-brand-name="{{ $brand['brand_name'] }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-brand"
                                            data-brand-id="{{ $brand['brand_id'] }}"
                                            data-brand-name="{{ $brand['brand_name'] }}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No brands found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($brands_pagination) && $brands_pagination['last_page'] > 1)
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <!-- Previous Page Link -->
                            @if($brands_pagination['current_page'] > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $brands_pagination['current_page'] - 1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            @endif

                            <!-- Page Numbers -->
                            @php
                                $startPage = max($brands_pagination['current_page'] - 2, 1);
                                $endPage = min($startPage + 4, $brands_pagination['last_page']);

                                if($endPage - $startPage < 4) {
                                    $startPage = max($endPage - 4, 1);
                                }
                            @endphp

                            @for($i = $startPage; $i <= $endPage; $i++)
                                <li class="page-item {{ $i == $brands_pagination['current_page'] ? 'active' : '' }}">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            <!-- Next Page Link -->
                            @if($brands_pagination['current_page'] < $brands_pagination['last_page'])
                                <li class="page-item">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $brands_pagination['current_page'] + 1]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBrandModalLabel">Add New Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('brands.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="brand_name">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="brand_name" name="brand_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Brand Modal -->
<div class="modal fade" id="editBrandModal" tabindex="-1" role="dialog" aria-labelledby="editBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBrandModalLabel">Edit Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editBrandForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_brand_name">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_brand_name" name="brand_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Brand Modal -->
<div class="modal fade" id="deleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="deleteBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBrandModalLabel">Delete Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the brand: <span id="delete_brand_name" class="font-weight-bold"></span>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteBrandForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Brand</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle Edit Brand button click
        $('.edit-brand').on('click', function() {
            const brandId = $(this).data('brand-id');
            const brandName = $(this).data('brand-name');

            $('#edit_brand_name').val(brandName);
            $('#editBrandForm').attr('action', `/brands/${brandId}`);
            $('#editBrandModal').modal('show');
        });

        // Handle Delete Brand button click
        $('.delete-brand').on('click', function() {
            const brandId = $(this).data('brand-id');
            const brandName = $(this).data('brand-name');

            $('#delete_brand_name').text(brandName);
            $('#deleteBrandForm').attr('action', `/brands/${brandId}`);
            $('#deleteBrandModal').modal('show');
        });

        // Initialize DataTable if there are entries (optional)
        if ($('#brandsTable tbody tr').length > 1) {
            $('#brandsTable').DataTable({
                "paging": false,  // Disable built-in pagination as we're using Laravel's
                "searching": false, // Disable built-in search as we're using our own
                "ordering": true,
                "info": false,
                "responsive": true
            });
        }
    });
</script>
@endpush
