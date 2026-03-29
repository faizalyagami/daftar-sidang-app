@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<style>
    .info-text {
        font-size: 11px;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .info-text i {
        margin-right: 3px;
    }
    
    .alert-info-custom {
        background: #e3f2fd;
        border-left: 4px solid #2196f3;
        border-radius: 10px;
        padding: 15px;
        margin-top: 20px;
    }
    
    .required-field::after {
        content: '*';
        color: red;
        margin-left: 4px;
    }
    
    .form-label {
        font-weight: 600;
        font-size: 14px;
    }
    
    .section-title {
        color: #667eea;
        margin: 20px 0 15px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card fade-in border-0 shadow-sm">
            <div class="card-header-custom">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus me-2"></i>
                    Tambah User Baru
                </h5>
                <small class="text-white-50">Isi data user dengan lengkap</small>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
                    @csrf
                    
                    <!-- Informasi Akun -->
                    <h6 class="section-title">
                        <i class="bi bi-envelope me-2"></i>
                        Informasi Akun
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-person text-primary"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-envelope text-primary"></i>
                                </span>
                                <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-lock text-primary"></i>
                                </span>
                                <input type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                       id="password"
                                       name="password" placeholder="Minimal 6 karakter" required>
                            </div>
                            <div class="info-text" id="passwordInfo">
                                <i class="bi bi-info-circle"></i> Password minimal 6 karakter
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-badge text-primary"></i>
                                </span>
                                <select class="form-select border-start-0 @error('role') is-invalid @enderror" 
                                        name="role" id="role" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="reviewer" {{ old('role') == 'reviewer' ? 'selected' : '' }}>Reviewer</option>
                                    <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                </select>
                            </div>
                            <div class="info-text">
                                <i class="bi bi-info-circle"></i> 
                                Admin: akses penuh | Reviewer: hanya review | Mahasiswa: daftar sidang
                            </div>
                            @error('role')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- DATA MAHASISWA (NPM) -->
                    <div id="mahasiswaFields" style="display: {{ old('role') == 'mahasiswa' ? 'block' : 'none' }};">
                        <h6 class="section-title">
                            <i class="bi bi-mortarboard me-2"></i>
                            Data Mahasiswa
                        </h6>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label required-field">NPM (Nomor Pokok Mahasiswa)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-qr-code text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 @error('npm') is-invalid @enderror" 
                                           id="npm"
                                           name="npm" value="{{ old('npm') }}" 
                                           placeholder="Contoh: 10050019026">
                                </div>
                                <div class="info-text" id="npmInfo">
                                    <i class="bi bi-info-circle"></i> 
                                    NPM akan digunakan sebagai <strong>Username</strong> dan <strong>Password default</strong>
                                </div>
                                @error('npm')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Tempat Lahir</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-geo-alt text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 @error('tempat_lahir') is-invalid @enderror" 
                                           name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Contoh: Bandung">
                                </div>
                                @error('tempat_lahir')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Tanggal Lahir</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-calendar text-primary"></i>
                                    </span>
                                    <input type="date" class="form-control border-start-0 @error('tanggal_lahir') is-invalid @enderror" 
                                           name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                                </div>
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">No Handphone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-phone text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 @error('no_hp') is-invalid @enderror" 
                                           name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890">
                                </div>
                                @error('no_hp')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Dosen Wali</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-person-badge text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 @error('dosen_wali') is-invalid @enderror" 
                                           name="dosen_wali" value="{{ old('dosen_wali') }}" placeholder="Contoh: Dr. Ahmad Rizal, M.Si">
                                </div>
                                @error('dosen_wali')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">IPK (Opsional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-graph-up text-primary"></i>
                                    </span>
                                    <input type="number" step="0.01" class="form-control border-start-0 @error('ipk') is-invalid @enderror" 
                                           name="ipk" value="{{ old('ipk') }}" placeholder="Contoh: 3.50" min="0" max="4">
                                </div>
                                @error('ipk')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- DATA ADMIN/REVIEWER (NIK) -->
                    <div id="adminReviewerFields" style="display: {{ (old('role') == 'admin' || old('role') == 'reviewer') ? 'block' : 'none' }};">
                        <h6 class="section-title">
                            <i class="bi bi-person-badge me-2"></i>
                            Data Pegawai
                        </h6>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label required-field">NIK (Nomor Induk Karyawan)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-card-text text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 @error('nik') is-invalid @enderror" 
                                           id="nik"
                                           name="nik" value="{{ old('nik') }}" 
                                           placeholder="Contoh: 198501012010011001">
                                </div>
                                <div class="info-text" id="nikInfo">
                                    <i class="bi bi-info-circle"></i> 
                                    NIK akan digunakan sebagai <strong>Username</strong> untuk login
                                </div>
                                @error('nik')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-briefcase text-primary"></i>
                                    </span>
                                    <select class="form-select border-start-0 @error('jabatan') is-invalid @enderror" 
                                            name="jabatan">
                                        <option value="">-- Pilih Jabatan --</option>
                                        <option value="Kepala Program Studi" {{ old('jabatan') == 'Kepala Program Studi' ? 'selected' : '' }}>Kepala Program Studi</option>
                                        <option value="Sekretaris Program Studi" {{ old('jabatan') == 'Sekretaris Program Studi' ? 'selected' : '' }}>Sekretaris Program Studi</option>
                                        <option value="Dosen" {{ old('jabatan') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                        <option value="Tenaga Pendidik" {{ old('jabatan') == 'Tenaga Pendidik' ? 'selected' : '' }}>Tenaga Pendidik</option>
                                        <option value="Staff Administrasi" {{ old('jabatan') == 'Staff Administrasi' ? 'selected' : '' }}>Staff Administrasi</option>
                                    </select>
                                </div>
                                @error('jabatan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No Handphone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-phone text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 @error('no_hp_pegawai') is-invalid @enderror" 
                                           name="no_hp_pegawai" value="{{ old('no_hp_pegawai') }}" placeholder="Contoh: 081234567890">
                                </div>
                                @error('no_hp_pegawai')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alert Info -->
                    <div class="alert alert-info-custom">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Informasi Penting:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Untuk <strong>Mahasiswa</strong>: NPM = Username, Password default = NPM (wajib diganti saat login)</li>
                            <li>Untuk <strong>Admin/Reviewer</strong>: NIK = Username, Password = isi manual</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-save"></i> Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const roleSelect = document.getElementById('role');
    const mahasiswaFields = document.getElementById('mahasiswaFields');
    const adminReviewerFields = document.getElementById('adminReviewerFields');
    const npmInput = document.getElementById('npm');
    const nikInput = document.getElementById('nik');
    const passwordInput = document.getElementById('password');
    const passwordInfo = document.getElementById('passwordInfo');
    const npmInfo = document.getElementById('npmInfo');
    const nikInfo = document.getElementById('nikInfo');
    
    // Toggle fields based on role
    function toggleFields() {
        const selectedRole = roleSelect.value;
        
        if (selectedRole === 'mahasiswa') {
            mahasiswaFields.style.display = 'block';
            adminReviewerFields.style.display = 'none';
            
            // Set required for mahasiswa fields
            document.querySelectorAll('#mahasiswaFields input').forEach(input => {
                if (input.name !== 'ipk') {
                    input.required = true;
                }
            });
            document.querySelectorAll('#adminReviewerFields input').forEach(input => {
                input.required = false;
            });
            
            // Update NPM info
            if (npmInput && npmInput.value) {
                updateNpmInfo(npmInput.value);
            }
            
            // Auto-set password hint
            if (passwordInfo) {
                passwordInfo.innerHTML = '<i class="bi bi-info-circle"></i> Password akan diisi otomatis dengan NPM';
            }
            
        } else if (selectedRole === 'admin' || selectedRole === 'reviewer') {
            mahasiswaFields.style.display = 'none';
            adminReviewerFields.style.display = 'block';
            
            // Set required for admin/reviewer fields
            document.querySelectorAll('#mahasiswaFields input').forEach(input => {
                input.required = false;
            });
            document.querySelectorAll('#adminReviewerFields input').forEach(input => {
                if (input.name !== 'jabatan' && input.name !== 'no_hp_pegawai') {
                    input.required = true;
                }
            });
            
            // Update NIK info
            if (nikInput && nikInput.value) {
                updateNikInfo(nikInput.value);
            }
            
            // Reset password info
            if (passwordInfo) {
                passwordInfo.innerHTML = '<i class="bi bi-info-circle"></i> Password minimal 6 karakter';
            }
            
        } else {
            mahasiswaFields.style.display = 'none';
            adminReviewerFields.style.display = 'none';
        }
    }
    
    // Update NPM info dynamically
    function updateNpmInfo(npm) {
        if (npmInfo && npm) {
            npmInfo.innerHTML = `<i class="bi bi-info-circle"></i> 
                Username: <strong>${npm}</strong> | 
                Password default: <strong>${npm}</strong> |
                <span class="text-warning">⚠️ Wajib diganti saat login pertama</span>`;
                
            // Auto-set password for mahasiswa
            if (passwordInput && !passwordInput.value) {
                passwordInput.value = npm;
                checkPasswordStrength(npm);
            }
        }
    }
    
    // Update NIK info dynamically
    function updateNikInfo(nik) {
        if (nikInfo && nik) {
            nikInfo.innerHTML = `<i class="bi bi-info-circle"></i> 
                Username: <strong>${nik}</strong> |
                <span class="text-success">✓ Gunakan NIK untuk login</span>`;
        }
    }
    
    // NPM input handler
    if (npmInput) {
        npmInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            updateNpmInfo(this.value);
        });
    }
    
    // NIK input handler
    if (nikInput) {
        nikInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            updateNikInfo(this.value);
        });
    }
    
    // Password strength checker
    function checkPasswordStrength(password) {
        if (!passwordInfo) return;
        
        if (password.length === 0) {
            passwordInfo.innerHTML = '<i class="bi bi-info-circle"></i> Password minimal 6 karakter';
            return;
        }
        
        let strength = 0;
        let message = '';
        let className = '';
        
        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        if (strength <= 2) {
            message = 'Lemah';
            className = 'text-danger';
        } else if (strength <= 4) {
            message = 'Sedang';
            className = 'text-warning';
        } else {
            message = 'Kuat';
            className = 'text-success';
        }
        
        passwordInfo.innerHTML = `<i class="bi bi-shield"></i> Kekuatan password: <strong class="${className}">${message}</strong>`;
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    }
    
    // Role change handler
    roleSelect.addEventListener('change', toggleFields);
    
    // Form submit loading state
    document.getElementById('userForm')?.addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner me-2"></span> Menyimpan...';
    });
    
    // Trigger on page load
    toggleFields();
    if (npmInput && npmInput.value) updateNpmInfo(npmInput.value);
    if (nikInput && nikInput.value) updateNikInfo(nikInput.value);
</script>
@endpush