@extends('layouts.app')

@section('title', 'Semua Pendaftaran')

@section('content')
    <style>
        /* Custom Pagination Styles */
        .custom-pagination {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 20px;
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .pagination-info {
            color: #6c757d;
            font-size: 14px;
            margin-right: auto;
        }

        .pagination {
            margin: 0;
            gap: 5px;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-link {
            border: none;
            padding: 8px 14px;
            font-size: 14px;
            color: #4a5568;
            background: white;
            border-radius: 10px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .pagination .page-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .pagination .active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .pagination .disabled .page-link {
            color: #cbd5e0;
            background: #f1f3f5;
            cursor: not-allowed;
        }

        .pagination .page-link i {
            font-size: 12px;
        }

        .per-page-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: 20px;
        }

        .per-page-selector label {
            margin: 0;
            font-size: 13px;
            color: #6c757d;
        }

        .per-page-selector select {
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: white;
            font-size: 13px;
            cursor: pointer;
        }

        /* Status Badge Styles */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }

        .status-review {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #17a2b8;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #28a745;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #dc3545;
        }

        .status-revision {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }

        .bulk-actions {
            background: #f0f4ff;
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            justify-content: space-between;
        }

        .bulk-actions.show {
            display: flex;
        }

        .select-all-checkbox {
            margin-right: 10px;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <!-- Filter Periode -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.pendaftaran') }}" class="row g-3 align-items-end">
                        <div class="col-auto">
                            <label class="form-label fw-semibold">Filter Periode Pendaftaran</label>
                            <select name="period_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Periode</option>
                                @foreach ($periods as $period)
                                    <option value="{{ $period->id }}"
                                        {{ request('period_id') == $period->id ? 'selected' : '' }}>
                                        {{ $period->semester }} {{ $period->tahun_akademik }}
                                        ({{ $period->start_date->format('d/m/Y') }} -
                                        {{ $period->end_date->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.pendaftaran') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Actions Panel -->
            <div class="bulk-actions" id="bulkActions">
                <div>
                    <span id="selectedCount">0</span> pendaftaran dipilih
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#bulkAssignModal">
                        <i class="bi bi-send"></i> Assign ke Reviewer
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="clearSelection">
                        <i class="bi bi-x-circle"></i> Batal Pilih
                    </button>
                </div>
            </div>

            <ul class="nav nav-tabs mb-4" id="pendaftaranTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="skripsi-tab" data-bs-toggle="tab" data-bs-target="#skripsi"
                        type="button" role="tab">
                        <i class="bi bi-file-text"></i> Sidang Skripsi
                        <span class="badge bg-primary">{{ $skripsi->total() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="metodologi-tab" data-bs-toggle="tab" data-bs-target="#metodologi"
                        type="button" role="tab">
                        <i class="bi bi-book"></i> Ujian Metodologi
                        <span class="badge bg-primary">{{ $metodologi->total() }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="skripsi" role="tabpanel">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAllSkripsi"
                                                    title="Hanya pendaftaran dengan status 'Menunggu Review' yang dapat dipilih">
                                            </th>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Periode</th>
                                            <th>NPM</th>
                                            <th>Nama</th>
                                            <th>Judul Skripsi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($skripsi as $item)
                                            @php
                                                $period = $item->academicPeriod;
                                                $isAssignable = in_array($item->status, ['pending']); // Hanya status pending yang bisa diassign
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="checkbox-skripsi"
                                                        data-id="{{ $item->id }}" data-type="skripsi"
                                                        {{ !$isAssignable ? 'disabled' : '' }}>
                                                </td>
                                                <td class="align-midle">{{ $loop->iteration }}</td>
                                                <td class="align-middle">{{ $item->created_at->format('d/m/Y') }}</td>
                                                <td class="align-middle">
                                                    @if ($period)
                                                        <span class="badge bg-secondary">{{ $period->semester }}
                                                            {{ $period->tahun_akademik }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="align-middle">{{ $item->mahasiswa->npm }}</td>
                                                <td class="align-middle">{{ $item->mahasiswa->user->name }}</td>
                                                <td class="align-middle">{{ Str::limit($item->judul_skripsi, 50) }}</td>
                                                <td class="align-middle">
                                                    @php
                                                        $statusText = '';
                                                        $statusClass = '';

                                                        switch ($item->status) {
                                                            case 'belum_daftar':
                                                                $statusText = 'Belum Daftar';
                                                                $statusClass = 'status-pending';
                                                                break;
                                                            case 'pending':
                                                                $statusText = 'Menunggu Review';
                                                                $statusClass = 'status-pending';
                                                                break;
                                                            case 'review':
                                                                $statusText = 'Direview';
                                                                $statusClass = 'status-review';
                                                                break;
                                                            case 'approved':
                                                                $statusText = 'Disetujui';
                                                                $statusClass = 'status-approved';
                                                                break;
                                                            case 'rejected':
                                                                $statusText = 'Ditolak';
                                                                $statusClass = 'status-rejected';
                                                                break;
                                                            case 'revision':
                                                                $statusText = 'Revisi';
                                                                $statusClass = 'status-revision';
                                                                break;
                                                            default:
                                                                $statusText = ucfirst($item->status);
                                                                $statusClass = 'status-pending';
                                                        }
                                                    @endphp
                                                    <span class="status-badge {{ $statusClass }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <a href="{{ route('admin.pendaftaran.skripsi.show', $item->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                                    <p class="text-muted mt-2 mb-0">Belum ada pendaftaran sidang skripsi</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Custom Pagination untuk Skripsi -->
                            @if ($skripsi->total() > $skripsi->perPage())
                                <div class="custom-pagination">
                                    <div class="pagination-info">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Menampilkan
                                        <strong>{{ $skripsi->firstItem() ?? 0 }}</strong>
                                        sampai
                                        <strong>{{ $skripsi->lastItem() ?? 0 }}</strong>
                                        dari
                                        <strong>{{ $skripsi->total() }}</strong>
                                        data
                                        @if (request('period_id'))
                                            <span class="text-muted"> - Filter periode aktif</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center">
                                        {{ $skripsi->appends(request()->query())->links('pagination::bootstrap-4') }}
                                        <div class="per-page-selector">
                                            <label>Baris per halaman:</label>
                                            <select id="perPageSelectSkripsi" class="form-select form-select-sm">
                                                <option value="10"
                                                    {{ request('per_page_skripsi', 10) == 10 ? 'selected' : '' }}>10
                                                </option>
                                                <option value="25"
                                                    {{ request('per_page_skripsi', 25) == 25 ? 'selected' : '' }}>25
                                                </option>
                                                <option value="50"
                                                    {{ request('per_page_skripsi', 50) == 50 ? 'selected' : '' }}>50
                                                </option>
                                                <option value="100"
                                                    {{ request('per_page_skripsi', 100) == 100 ? 'selected' : '' }}>100
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="metodologi" role="tabpanel">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Periode</th>
                                            <th>NPM</th>
                                            <th>Nama</th>
                                            <th>Judul Penelitian</th>
                                            <th>Pembimbing</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($metodologi as $index => $item)
                                            @php
                                                $period = $item->academicPeriod;
                                                $isAssignable = in_array($item->status, ['pending']); // Hanya status pending yang bisa diassign
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="checkbox-skripsi"
                                                        data-id="{{ $item->id }}" data-type="skripsi"
                                                        {{ !$isAssignable ? 'disabled' : '' }}>
                                                </td>
                                                <td class="align-middle">{{ $loop->iteration }}</td>
                                                <td class="align-middle">{{ $metodologi->firstItem() + $index }}</td>
                                                <td class="align-middle">{{ $item->created_at->format('d/m/Y') }}</td>
                                                <td class="align-middle">
                                                    @if ($period)
                                                        <span class="badge bg-secondary">{{ $period->semester }}
                                                            {{ $period->tahun_akademik }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="align-middle">{{ $item->mahasiswa->npm }}</td>
                                                <td class="align-middle">{{ $item->mahasiswa->user->name }}</td>
                                                <td class="align-middle">{{ Str::limit($item->judul_metodologi, 50) }}
                                                </td>
                                                <td class="align-middle">
                                                    @php
                                                        $statusText = '';
                                                        $statusClass = '';

                                                        switch ($item->status) {
                                                            case 'belum_daftar':
                                                                $statusText = 'Belum Daftar';
                                                                $statusClass = 'status-pending';
                                                                break;
                                                            case 'pending':
                                                                $statusText = 'Menunggu Review';
                                                                $statusClass = 'status-pending';
                                                                break;
                                                            case 'review':
                                                                $statusText = 'Direview';
                                                                $statusClass = 'status-review';
                                                                break;
                                                            case 'approved':
                                                                $statusText = 'Disetujui';
                                                                $statusClass = 'status-approved';
                                                                break;
                                                            case 'rejected':
                                                                $statusText = 'Ditolak';
                                                                $statusClass = 'status-rejected';
                                                                break;
                                                            case 'revision':
                                                                $statusText = 'Revisi';
                                                                $statusClass = 'status-revision';
                                                                break;
                                                            default:
                                                                $statusText = ucfirst($item->status);
                                                                $statusClass = 'status-pending';
                                                        }
                                                    @endphp
                                                    <span class="status-badge {{ $statusClass }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <a href="{{ route('admin.pendaftaran.metodologi.show', $item->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                                    <p class="text-muted mt-2 mb-0">Belum ada pendaftaran sidang skripsi
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Custom Pagination untuk Metodologi -->
                            @if ($metodologi->total() > $metodologi->perPage())
                                <div class="custom-pagination">
                                    <div class="pagination-info">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Menampilkan
                                        <strong>{{ $metodologi->firstItem() ?? 0 }}</strong>
                                        sampai
                                        <strong>{{ $metodologi->lastItem() ?? 0 }}</strong>
                                        dari
                                        <strong>{{ $metodologi->total() }}</strong>
                                        data
                                        @if (request('period_id'))
                                            <span class="text-muted"> - Filter periode aktif</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center">
                                        {{ $metodologi->appends(request()->query())->links('pagination::bootstrap-4') }}
                                        <div class="per-page-selector">
                                            <label>Baris per halaman:</label>
                                            <select id="perPageSelectMetodologi" class="form-select form-select-sm">
                                                <option value="10"
                                                    {{ request('per_page_metodologi') == 10 ? 'selected' : '' }}>10
                                                </option>
                                                <option value="25"
                                                    {{ request('per_page_metodologi') == 25 ? 'selected' : '' }}>25
                                                </option>
                                                <option value="50"
                                                    {{ request('per_page_metodologi') == 50 ? 'selected' : '' }}>50
                                                </option>
                                                <option value="100"
                                                    {{ request('per_page_metodologi') == 100 ? 'selected' : '' }}>100
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bulkAssignModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign ke Reviewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong id="bulkSelectedCount">0</strong> pendaftaran akan diassign ke reviewer.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Reviewer</label>
                        <select id="bulkReviewerId" class="form-select" required>
                            <option value="">-- Pilih Reviewer --</option>
                            @foreach ($reviewers as $reviewer)
                                <option value="{{ $reviewer->id }}">{{ $reviewer->name }} ({{ $reviewer->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea id="bulkNotes" class="form-control" rows="3" placeholder="Catatan untuk reviewer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmBulkAssign">Assign</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Simpan selected items
        let selectedSkripsi = [];
        let selectedMetodologi = [];

        function updateBulkActions() {
            const total = selectedSkripsi.length + selectedMetodologi.length;
            const bulkActions = document.getElementById('bulkActions');
            const selectedCountSpan = document.getElementById('selectedCount');
            const bulkSelectedCountSpan = document.getElementById('bulkSelectedCount');

            if (total > 0) {
                bulkActions.classList.add('show');
                selectedCountSpan.textContent = total;
                if (bulkSelectedCountSpan) bulkSelectedCountSpan.textContent = total;
            } else {
                bulkActions.classList.remove('show');
            }
        }

        document.getElementById('selectAllSkripsi')?.addEventListener('change', function(e) {
            const isChecked = e.target.checked;
            document.querySelectorAll('.checkbox-skripsi').forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = isChecked;
                    const id = checkbox.dataset.id;
                    if (isChecked) {
                        if (!selectedSkripsi.includes(id)) {
                            selectedSkripsi.push(id);
                        }
                    } else {
                        const index = selectedSkripsi.indexOf(id);
                        if (index > -1) selectedSkripsi.splice(index, 1);
                    }
                }
            });
            updateBulkActions();
        });

        // Select all metodologi
        document.getElementById('selectAllMetodologi')?.addEventListener('change', function(e) {
            const isChecked = e.target.checked;
            document.querySelectorAll('.checkbox-metodologi').forEach(checkbox => {
                checkbox.checked = isChecked;
                const id = checkbox.dataset.id;
                const type = checkbox.dataset.type;
                if (isChecked) {
                    if (!selectedMetodologi.includes(id)) {
                        selectedMetodologi.push(id);
                    }
                } else {
                    const index = selectedMetodologi.indexOf(id);
                    if (index > -1) selectedMetodologi.splice(index, 1);
                }
            });
            updateBulkActions();
        });

        // Individual checkbox skripsi
        document.querySelectorAll('.checkbox-skripsi').forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                const id = this.dataset.id;
                if (this.checked) {
                    if (!selectedSkripsi.includes(id)) selectedSkripsi.push(id);
                } else {
                    const index = selectedSkripsi.indexOf(id);
                    if (index > -1) selectedSkripsi.splice(index, 1);
                }
                updateBulkActions();
            });
        });

        // Individual checkbox metodologi
        document.querySelectorAll('.checkbox-metodologi').forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                const id = this.dataset.id;
                if (this.checked) {
                    if (!selectedMetodologi.includes(id)) selectedMetodologi.push(id);
                } else {
                    const index = selectedMetodologi.indexOf(id);
                    if (index > -1) selectedMetodologi.splice(index, 1);
                }
                updateBulkActions();
            });
        });

        // Clear selection
        document.getElementById('clearSelection')?.addEventListener('click', function() {
            selectedSkripsi = [];
            selectedMetodologi = [];
            document.querySelectorAll('.checkbox-skripsi, .checkbox-metodologi').forEach(cb => cb.checked = false);
            updateBulkActions();
        });

        // Bulk assign
        document.getElementById('confirmBulkAssign')?.addEventListener('click', function() {
            const reviewerId = document.getElementById('bulkReviewerId').value;
            const notes = document.getElementById('bulkNotes').value;

            if (!reviewerId) {
                Swal.fire('Error', 'Pilih reviewer terlebih dahulu', 'error');
                return;
            }

            if (selectedSkripsi.length === 0 && selectedMetodologi.length === 0) {
                Swal.fire('Error', 'Tidak ada pendaftaran yang dipilih', 'error');
                return;
            }

            fetch('{{ route('admin.pendaftaran.bulk-assign') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        skripsi_ids: selectedSkripsi,
                        metodologi_ids: selectedMetodologi,
                        reviewer_id: reviewerId,
                        notes: notes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                });
        });

        document.getElementById('perPageSelectSkripsi')?.addEventListener('change', function() {
            const url = new URL(window.location.href);

            url.searchParams.set('per_page_skripsi', this.value);
            url.searchParams.set('page_skripsi', 1); // reset ke halaman pertama

            window.location.href = url.toString();
        });

        document.getElementById('perPageSelectMetodologi')?.addEventListener('change', function() {
            const url = new URL(window.location.href);

            url.searchParams.set('per_page_metodologi', this.value);
            url.searchParams.set('page_metodologi', 1); // reset ke halaman pertama

            window.location.href = url.toString();
        });
    </script>
@endpush
