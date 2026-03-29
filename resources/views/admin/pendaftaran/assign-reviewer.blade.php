@extends('layouts.app')

@section('title', 'Assign ke Reviewer')

@section('content')
<style>
    .reviewer-card {
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .reviewer-card:hover {
        border-color: #667eea;
        background: #f8f9fa;
        transform: translateY(-3px);
    }
    
    .reviewer-card.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
    }
    
    .load-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .load-low { background: #d4edda; color: #155724; }
    .load-medium { background: #fff3cd; color: #856404; }
    .load-high { background: #f8d7da; color: #721c24; }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus me-2"></i>
                        Assign Pendaftaran ke Reviewer
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informasi Pendaftaran -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Mahasiswa:</strong> {{ $pendaftaran->mahasiswa->user->name }}<br>
                                <strong>NPM:</strong> {{ $pendaftaran->mahasiswa->npm }}
                            </div>
                            <div class="col-md-6">
                                <strong>Judul:</strong> 
                                @if($type == 'skripsi')
                                    {{ $pendaftaran->judul_skripsi }}
                                @else
                                    {{ $pendaftaran->judul_penelitian }}
                                @endif
                                <br>
                                <strong>Tanggal Daftar:</strong> {{ $pendaftaran->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pilihan Assign -->
                    <form action="{{ route('admin.pendaftaran.assign', ['type' => $type, 'id' => $pendaftaran->id]) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="assignment_type" id="autoAssign" value="auto" checked>
                                <label class="form-check-label fw-bold" for="autoAssign">
                                    <i class="bi bi-robot"></i> Assign Otomatis (Load Balancing)
                                </label>
                                <p class="text-muted ms-4 mb-0 small">
                                    Sistem akan memilih reviewer dengan beban kerja paling ringan
                                </p>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="assignment_type" id="manualAssign" value="manual">
                                <label class="form-check-label fw-bold" for="manualAssign">
                                    <i class="bi bi-person-check"></i> Assign Manual
                                </label>
                                <p class="text-muted ms-4 mb-0 small">
                                    Pilih reviewer secara manual dari daftar berikut
                                </p>
                            </div>
                        </div>
                        
                        <!-- Daftar Reviewer dengan Statistik -->
                        <div id="reviewerList" style="display: none;">
                            <h6 class="mb-3">Pilih Reviewer:</h6>
                            <div class="row">
                                @foreach($reviewers as $reviewer)
                                @php
                                    $reviewerStat = collect($stats)->firstWhere('reviewer.id', $reviewer->id);
                                    $load = $reviewerStat['pending'] ?? 0;
                                    $loadClass = $load <= 2 ? 'load-low' : ($load <= 5 ? 'load-medium' : 'load-high');
                                    $loadText = $load <= 2 ? 'Ringan' : ($load <= 5 ? 'Sedang' : 'Berat');
                                @endphp
                                <div class="col-md-6">
                                    <div class="reviewer-card" onclick="selectReviewer({{ $reviewer->id }})" id="reviewer-card-{{ $reviewer->id }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ $reviewer->name }}</strong><br>
                                                <small class="text-muted">{{ $reviewer->email }}</small>
                                            </div>
                                            <span class="load-badge {{ $loadClass }}">
                                                <i class="bi bi-inbox"></i> {{ $load }} tugas ({{ $loadText }})
                                            </span>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-check-circle"></i> Selesai: {{ $reviewerStat['completed'] ?? 0 }} |
                                                <i class="bi bi-hourglass-split"></i> Proses: {{ $reviewerStat['in_progress'] ?? 0 }}
                                            </small>
                                        </div>
                                        <div class="mt-2">
                                            <div class="progress" style="height: 5px;">
                                                @php
                                                    $total = ($reviewerStat['total'] ?? 0);
                                                    $percentage = $total > 0 ? ($reviewerStat['completed'] / $total) * 100 : 0;
                                                @endphp
                                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <input type="hidden" name="reviewer_id" id="selected_reviewer_id">
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('admin.pendaftaran.' . $type . '.show', $pendaftaran->id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Assign ke Reviewer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle reviewer list
    document.querySelectorAll('input[name="assignment_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const reviewerList = document.getElementById('reviewerList');
            if (this.value === 'manual') {
                reviewerList.style.display = 'block';
            } else {
                reviewerList.style.display = 'none';
                document.getElementById('selected_reviewer_id').value = '';
                document.querySelectorAll('.reviewer-card').forEach(card => {
                    card.classList.remove('selected');
                });
            }
        });
    });
    
    // Select reviewer
    function selectReviewer(reviewerId) {
        document.querySelectorAll('.reviewer-card').forEach(card => {
            card.classList.remove('selected');
        });
        document.getElementById('reviewer-card-' + reviewerId).classList.add('selected');
        document.getElementById('selected_reviewer_id').value = reviewerId;
    }
</script>
@endsection