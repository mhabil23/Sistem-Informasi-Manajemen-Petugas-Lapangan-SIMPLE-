@extends('layouts.auth')

@section('title', 'Masuk ke Sistem - SIMPLE BPS Kolaka Utara')

@section('content')
<div class="auth-bento-card">

    <!-- ==============================================================
         LEFT HERO SHOWCASE PANE (IDENTITY, SIMPLE HIGHLIGHT & ACRONYM)
         ============================================================== -->
    <div class="auth-hero-pane">
        <!-- Background Art Accent -->
        <div class="hero-bg-accent"></div>

        <!-- Top: Logo & Header BPS Kolaka Utara -->
        <div>
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="p-2 bg-white rounded-4 shadow-sm border d-flex align-items-center justify-content-center" 
                     style="width: 58px; height: 58px; border-color: rgba(243, 112, 33, 0.25) !important;">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo BPS Kolaka Utara" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div>
                    <span class="d-block text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.08em;">
                        Badan Pusat Statistik
                    </span>
                    <span class="d-block fw-extrabold text-dark" style="font-size: 1.05rem; font-weight: 800; line-height: 1.25;">
                        Kabupaten Kolaka Utara
                    </span>
                </div>
            </div>

            <!-- Pill Badge -->
            <div class="mb-2">
                <span class="badge text-white px-2.5 py-1 rounded-pill" style="background: #F37021; font-size: 0.72rem; font-weight: 700;">
                    <i class="bi bi-patch-check-fill me-1"></i> Portal Resmi Lapangan
                </span>
            </div>

            <!-- Prominent SIMPLE Title Highlight -->
            <div class="mb-3">
                <h1 class="display-5 fw-extrabold mb-1" style="color: #0F172A; font-weight: 850; letter-spacing: -0.025em; line-height: 1.15;">
                    SIMPLE<span style="color: var(--bps-orange);">.</span>
                </h1>
            </div>

            <!-- ACRONYM HIGHLIGHT CARD (S-I-M-P-E-L) -->
            <div class="p-3 mb-4" style="background: rgba(255, 255, 255, 0.9); border: 1.5px solid rgba(243, 112, 33, 0.3); border-radius: 18px; box-shadow: 0 4px 18px rgba(243, 112, 33, 0.06); backdrop-filter: blur(8px);">
                <div class="fw-bold" style="font-size: 0.96rem; line-height: 1.55; color: #1E293B;">
                    <span class="text-bps-orange fw-bolder" style="font-size: 1.15rem;">S</span>istem 
                    <span class="text-bps-orange fw-bolder" style="font-size: 1.15rem;">I</span>nformasi 
                    <span class="text-bps-orange fw-bolder" style="font-size: 1.15rem;">M</span>anajemen 
                    <span class="text-bps-orange fw-bolder" style="font-size: 1.15rem;">P</span>elayanan &amp; 
                    <span class="text-bps-orange fw-bolder" style="font-size: 1.15rem;">E</span>valuasi 
                    <span class="text-bps-orange fw-bolder" style="font-size: 1.15rem;">L</span>apangan
                </div>
            </div>
        </div>

        <!-- Bottom Motto Box -->
        <div class="pt-3 border-top mt-auto" style="border-color: rgba(243, 112, 33, 0.18) !important;">
            <div class="d-flex align-items-start gap-2 text-muted" style="font-size: 0.78rem; font-style: italic;">
                <i class="bi bi-quote text-bps-orange fs-5 lh-1 flex-shrink-0"></i>
                <div>
                    BPS Kolut Bersih, BerAKHLAK, Bersinergi dan Hadir Melayani.
                    <span class="d-block fw-bold text-dark not-italic mt-0.5" style="font-style: normal; font-size: 0.72rem;">
                        &mdash; BPS Kabupaten Kolaka Utara
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         RIGHT FORM PANE (LOGIN FORM & DEMO CREDENTIALS)
         ============================================================== -->
    <div class="auth-form-pane">
        <!-- Form Header -->
        <div class="mb-4">
            <h2 class="fw-bold mb-1" style="font-size: 1.45rem; color: #0F172A; letter-spacing: -0.015em;">
                Masuk ke Portal
            </h2>
            <p class="text-muted small mb-0">
                Silakan masukkan username atau email dan kata sandi akun Anda.
            </p>
        </div>

        <!-- Feedback & Error Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3 rounded-3" role="alert" style="font-size: 0.82rem;">
                <i class="bi bi-check-circle-fill me-2 fs-6 flex-shrink-0"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3 rounded-3" role="alert" style="font-size: 0.82rem;">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-6 flex-shrink-0"></i>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->has('login'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3 rounded-3" role="alert" style="font-size: 0.82rem;">
                <i class="bi bi-x-circle-fill me-2 fs-6 flex-shrink-0"></i>
                <div class="flex-grow-1">{{ $errors->first('login') }}</div>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.post') }}" class="needs-validation">
            @csrf

            <!-- Username / Email Field -->
            <div class="mb-3">
                <label for="login" class="form-label fw-bold text-dark small mb-1" style="font-size: 0.78rem; letter-spacing: 0.02em;">
                    USERNAME ATAU EMAIL
                </label>
                <div class="input-group auth-input-group">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0 ps-1 @error('login') is-invalid @enderror" 
                           id="login" 
                           name="login" 
                           value="{{ old('login') }}" 
                           placeholder="Masukkan username atau email" 
                           required 
                           autofocus>
                </div>
            </div>

            <!-- Password Field -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label fw-bold text-dark small mb-0" style="font-size: 0.78rem; letter-spacing: 0.02em;">
                        KATA SANDI
                    </label>
                </div>
                <div class="input-group auth-input-group">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-shield-lock-fill"></i>
                    </span>
                    <input type="password" 
                           class="form-control border-start-0 border-end-0 ps-1 @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Masukkan kata sandi" 
                           required>
                    <button class="btn btn-outline-secondary auth-password-toggle border-start-0" 
                            type="button" 
                            id="togglePassword" 
                            title="Tampilkan / Sembunyikan Password">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small mt-1" style="font-size: 0.75rem;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me & Version Badge -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label text-muted small fw-medium" for="remember" style="font-size: 0.8rem;">
                        Ingat Saya
                    </label>
                </div>
                <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill" style="font-size: 0.68rem; font-weight: 600;">
                    v1.2 &bull; Kolaka Utara
                </span>
            </div>

            <!-- Submit Button (BPS Orange) -->
            <button type="submit" class="btn btn-bps-primary w-100 py-2.5 fs-6 shadow-sm d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-in-right fs-5"></i>
                <span>Masuk ke Sistem</span>
            </button>
        </form>

        <!-- Footer Copyright -->
        <div class="text-center mt-4 pt-3 border-top" style="border-color: #E2E8F0 !important;">
            <span class="text-muted" style="font-size: 0.72rem;">
                &copy; {{ date('Y') }} BPS Kabupaten Kolaka Utara &bull; Hak Cipta Dilindungi
            </span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIcon.classList.toggle('bi-eye', !isPassword);
            eyeIcon.classList.toggle('bi-eye-slash', isPassword);
        });
    }
</script>
@endpush
