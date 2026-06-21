@extends('layouts.app')
@section('title', 'Portofolio Siswa')
@section('page-title', 'Portofolio Siswa')

@section('content')
<div class="space-y-5 fade-in" x-data="portofolioModule()">

    {{-- ── HEADER ── --}}
    <div class="card p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm" style="background: var(--accent-lt); color: var(--accent);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold" style="color: var(--text-1);">Portofolio kegiatan</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        @if($currentPeriode)
                            <span class="badge badge-blue text-[9px] px-2.5 py-0.5 uppercase tracking-wider">{{ $currentPeriode->nama_periode }}</span>
                            <span class="badge text-[9px] px-2.5 py-0.5 uppercase tracking-wider {{ $currentPeriode->status === 'aktif' ? 'badge-bsb' : 'badge-nonaktif' }}">{{ $currentPeriode->status === 'aktif' ? 'Aktif' : 'Final' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Filter PERIODE --}}
                @if(isset($listPeriode) && $listPeriode->count() > 0)
                    <form action="{{ route('guru.portofolio.index') }}" method="GET" id="periodeForm" class="relative">
                        <select name="periode_id" class="form-select" style="padding-left: 36px;" onchange="document.getElementById('periodeForm').submit()">
                            @foreach($listPeriode as $p)
                                <option value="{{ $p->id_periode }}" class="text-gray-900"
                                    {{ $currentPeriode && $currentPeriode->id_periode == $p->id_periode ? 'selected' : '' }}>
                                    {{ $p->nama_periode }} - {{ $p->tahunAjaran->nama ?? '—' }}
                                    @if($p->status === 'aktif') (Aktif) @elseif($p->status === 'final') (Final) @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </form>
                @endif

                @if($currentPeriode && $currentPeriode->status !== 'final')
                    <button type="button" @click="openModal()"
                            class="btn btn-green btn-sm flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah Portofolio
                    </button>
                @else
                    <button type="button" disabled
                            class="btn btn-gray btn-sm flex items-center gap-2 whitespace-nowrap opacity-50 cursor-not-allowed"
                            title="{{ !$currentPeriode ? 'Belum ada periode penilaian aktif.' : 'Periode ini sudah final. Data tidak dapat ditambah.' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah Portofolio
                    </button>
                @endif
            </div>
        </div>

        {{-- FILTER FORM --}}
        <div class="mt-4 pt-4 border-t" style="border-color: var(--border);">
            <form action="{{ route('guru.portofolio.index') }}" method="GET" class="flex flex-wrap gap-2 items-center">
                {{-- Simpan periode_id --}}
                <input type="hidden" name="periode_id" value="{{ $currentPeriode?->id_periode }}">

                {{-- Filter SISWA --}}
                <div class="relative flex-1 min-w-[180px]">
                    <select name="siswa_id" class="form-select" style="padding-left: 40px;">
                        <option value="">Semua siswa</option>
                        @foreach($siswa as $s)
                            <option value="{{ $s->id_siswa }}" {{ request('siswa_id') == $s->id_siswa ? 'selected' : '' }} class="text-gray-900" style="color: #000;">
                                ({{ $s->kelas->nama_kelas ?? '—' }}) {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>

                {{-- Filter MINGGU --}}
                <div class="relative w-full md:w-44">
                    <select name="minggu_id" class="form-select" style="padding-left: 40px;">
                        <option value="">Semua minggu</option>
                        @foreach($minggu as $m)
                            <option value="{{ $m->id_minggu }}" {{ request('minggu_id') == $m->id_minggu ? 'selected' : '' }} class="text-gray-900" style="color: #000;">Minggu {{ $m->minggu_ke }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                <button type="submit" class="btn btn-blue btn-sm whitespace-nowrap">Filter</button>

                @if(request('siswa_id') || request('minggu_id'))
                    <a href="{{ route('guru.portofolio.index', array_filter(['periode_id' => $currentPeriode?->id_periode])) }}" class="btn btn-gray btn-sm flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>


    {{-- ── GRID ── --}}
    @if($portofolio->count() > 0)
        {{-- Section header --}}
        <div class="flex items-center justify-between px-2">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Karya & Portofolio</h3>
            <span class="badge badge-gray px-3 py-1 text-[9px] font-black uppercase">{{ $portofolio->total() }} Karya</span>
        </div>

        @php
            $groupedByWeek = $portofolio->getCollection()->groupBy(fn($p) => $p->minggu->minggu_ke ?? 0);
        @endphp

        @foreach($groupedByWeek as $mingguKe => $weekItems)
            <div class="space-y-4">
                <div class="flex items-center gap-3 px-1">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shadow-sm" style="background: var(--accent-lt); border: 1px solid var(--border); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-black text-gray-700 uppercase tracking-widest">Minggu {{ $mingguKe }}</h4>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $weekItems->count() }} karya</p>
                    </div>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($weekItems as $p)
            @php 
                $isB1 = ($p->siswa->kelas->nama_kelas ?? '') === 'B1';
                $borderColor = $isB1 ? '#6b7280' : 'var(--accent)'; // Gray for B1, Green for others
            @endphp
            <div class="card p-4 relative group flex flex-col" style="border-left: 3px solid {{ $borderColor }};">
                <div class="relative aspect-[4/3] rounded-xl overflow-hidden mb-4" style="background: var(--bg); border: 1px solid var(--border);">
                    @if($p->images->count() > 0)
                        <img src="{{ asset('storage/' . $p->images->first()->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @if($p->images->count() > 1)
                            <div class="absolute top-2 right-2 px-2 py-1 bg-black/50 backdrop-blur-sm rounded-lg text-[9px] font-bold text-white border border-white/10 uppercase tracking-widest">+{{ $p->images->count() - 1 }}</div>
                        @endif
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center" style="color: var(--text-3);">
                            <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[9px] font-bold uppercase tracking-widest">No Photo</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="badge badge-blue text-[9px]">Minggu {{ $p->minggu->minggu_ke }}</span>
                        <span class="text-[9px] font-medium" style="color: var(--text-3);">{{ $p->created_at?->diffForHumans() }}</span>
                    </div>
                    <h3 class="font-semibold truncate text-[12px] uppercase tracking-wide" style="color: var(--text-1);">{{ $p->judul }}</h3>
                    <p class="text-[10px] font-medium mt-1 flex items-center gap-1.5" style="color: var(--text-2);">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--accent);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $p->siswa->name }}
                        </span>
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest {{ $isB1 ? 'bg-gray-100 text-gray-600' : 'bg-var(--accent-lt) text-var(--accent)' }}">
                            {{ $p->siswa->kelas->nama_kelas ?? '—' }}
                        </span>
                    </p>
                </div>
                <div class="mt-4 pt-4 flex gap-2" style="border-top: 1px solid var(--border);">
                    <a href="{{ route('guru.portofolio.show', $p->id_portofolio) }}"
                       class="flex-1 btn btn-gray btn-sm flex justify-center">
                        Detail
                    </a>
                    @if($p->minggu->periode->status !== 'final')
                        <button @click="openModal({{ json_encode($p->load('images')) }})"
                                class="btn btn-blue btn-sm px-3" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                    @endif
                </div>
            </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <div class="mt-6">{{ $portofolio->appends(request()->query())->links() }}</div>
    @else
        <div class="card p-20 text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: var(--bg); border: 1px solid var(--border); color: var(--border);">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-semibold text-sm" style="color: var(--text-2);">Belum ada portofolio</h3>
            <p class="text-xs mt-2 max-w-xs mx-auto" style="color: var(--text-3);">Klik tombol "Tambah portofolio" di atas untuk mulai mendokumentasikan kegiatan siswa.</p>
        </div>
    @endif

    {{-- Include Modal Partial --}}
    @include('guru.portofolio_modal')

</div>

@push('scripts')
<script>
    function portofolioModule() {
        return {
            formOpen: false,
            isEdit: false,
            editId: null,
            existingImages: [],
            newImages: [],      // { url, file } untuk preview sebelum upload
            selectedFiles: [],  // File[] aktual untuk dikirim
            
            // UX States
            isProcessing: false, // Sedang kompresi gambar
            submitting: false,   // Sedang upload ke server
            uploadProgress: 0,   // Persentase upload
            
            // Camera Logic
            showCamera: false,
            cameraStream: null,
            currentFacingMode: 'environment',
            flash: false,

            openModal(data = null) {
                if (data) {
                    this.isEdit = true;
                    this.editId = data.id_portofolio;
                    this.formData = { siswa_id: data.siswa_id, minggu_id: data.minggu_id, judul: data.judul, deskripsi: data.deskripsi };
                    this.existingImages = data.images || [];
                } else {
                    this.isEdit = false;
                    this.editId = null;
                    this.formData = { siswa_id: '', minggu_id: '', judul: '', deskripsi: '' };
                    this.existingImages = [];
                }
                this.newImages = [];
                this.selectedFiles = [];
                this.showCamera = false;
                this.formOpen = true;
                this.isProcessing = false;
                this.submitting = false;
                this.uploadProgress = 0;
                document.body.style.overflow = 'hidden';
            },

            closeForm() {
                this.stopCamera();
                this.formOpen = false;
                this.newImages = [];
                this.selectedFiles = [];
                document.body.style.overflow = '';
            },

            async startCamera() {
                if (this.showCamera) {
                    this.stopCamera();
                    return;
                }
                try {
                    const constraints = { 
                        video: { 
                            facingMode: this.currentFacingMode,
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        } 
                    };
                    this.cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
                    
                    const video = document.getElementById('cameraVideo');
                    if (video) {
                        video.srcObject = this.cameraStream;
                    }
                    
                    this.showCamera = true;
                } catch (err) {
                    console.error("Camera access denied:", err);
                    alert("Gagal mengakses kamera. Pastikan Anda telah memberikan izin kamera di browser.");
                }
            },

            stopCamera() {
                if (this.cameraStream) {
                    this.cameraStream.getTracks().forEach(track => track.stop());
                }
                this.showCamera = false;
                this.cameraStream = null;
            },

            async switchCamera() {
                this.currentFacingMode = this.currentFacingMode === 'environment' ? 'user' : 'environment';
                if (this.showCamera) {
                    this.stopCamera();
                    await this.startCamera();
                }
            },

            takePhoto() {
                const video = document.getElementById('cameraVideo');
                if (!video || video.videoWidth === 0) return;

                // Visual Feedback
                this.flash = true;
                setTimeout(() => this.flash = false, 150);

                const canvas = document.createElement('canvas');
                const MAX_SIZE = 800;
                let width = video.videoWidth;
                let height = video.videoHeight;

                if (width > height) {
                    if (width > MAX_SIZE) {
                        height *= MAX_SIZE / width;
                        width = MAX_SIZE;
                    }
                } else {
                    if (height > MAX_SIZE) {
                        width *= MAX_SIZE / height;
                        height = MAX_SIZE;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                
                if (this.currentFacingMode === 'user') {
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                }
                
                ctx.drawImage(video, 0, 0, width, height);
                const dataUrl = canvas.toDataURL('image/jpeg', 0.4);
                
                canvas.toBlob((blob) => {
                    const filename = `camera_${Date.now()}.jpg`;
                    const file = new File([blob], filename, { type: 'image/jpeg' });
                    
                    this.newImages.push({ url: dataUrl, name: filename });
                    this.selectedFiles.push(file);
                    this._syncFilesToInput();
                }, 'image/jpeg', 0.4);
            },

            async handleFileSelect(e) {
                const files = Array.from(e.target.files);
                if (!files.length) return;

                this.isProcessing = true;
                for (const file of files) {
                    if (!file.type.startsWith('image/')) continue;
                    
                    try {
                        const compressed = await this._compressImage(file);
                        this.selectedFiles.push(compressed.file);
                        this.newImages.push({ url: compressed.url, name: file.name });
                    } catch (err) {
                        console.error("Compression error:", err);
                    }
                }
                this.isProcessing = false;

                this._syncFilesToInput();
                e.target.value = '';
            },

            _compressImage(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const MAX_SIZE = 1000;
                            let width = img.width;
                            let height = img.height;

                            if (width > height) {
                                if (width > MAX_SIZE) {
                                    height *= MAX_SIZE / width;
                                    width = MAX_SIZE;
                                }
                            } else {
                                if (height > MAX_SIZE) {
                                    width *= MAX_SIZE / height;
                                    height = MAX_SIZE;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            const dataUrl = canvas.toDataURL('image/jpeg', 0.4);
                            canvas.toBlob((blob) => {
                                const compressedFile = new File([blob], file.name, { type: 'image/jpeg' });
                                resolve({ file: compressedFile, url: dataUrl });
                            }, 'image/jpeg', 0.4);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            },

            async submitForm() {
                if (this.submitting) return;

                const form = document.getElementById('portofolioForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                this.submitting = true;
                this.uploadProgress = 0;

                const formData = new FormData(form);
                
                // Pastikan file tersync (Laravel images[])
                // masterFileInput sudah memiliki file hasil kompresi

                const xhr = new XMLHttpRequest();
                const url = this.isEdit ? `{{ url('guru/portofolio') }}/${this.editId}` : `{{ route('guru.portofolio.store') }}`;
                
                xhr.open('POST', url, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');

                // Progress Tracker
                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable) {
                        this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                    }
                };

                xhr.onload = () => {
                    let res;
                    try {
                        res = JSON.parse(xhr.responseText);
                    } catch(e) {
                        res = { success: false, message: 'Server error: Terjadi kesalahan pada sistem.' };
                    }

                    if (xhr.status >= 200 && xhr.status < 300 && res.success) {
                        window.location.href = res.redirect || '{{ route('guru.portofolio.index') }}';
                    } else if (xhr.status === 422 && res.errors) {
                        this.submitting = false;
                        let errorHtml = '<ul class="text-left text-sm space-y-1 ml-4 list-disc">';
                        for (const key in res.errors) {
                            errorHtml += `<li>${res.errors[key][0]}</li>`;
                        }
                        errorHtml += '</ul>';
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Input',
                            html: errorHtml,
                            confirmButtonText: 'Perbaiki'
                        });
                    } else {
                        this.submitting = false;
                        this.closeForm();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: res.message || 'Gagal menyimpan data.',
                            confirmButtonText: 'Tutup'
                        });
                    }
                };

                xhr.onerror = () => {
                    this.submitting = false;
                    alert('Koneksi terputus atau server tidak merespons.');
                };

                xhr.send(formData);
            },

            removePreview(idx) {
                this.newImages.splice(idx, 1);
                this.selectedFiles.splice(idx, 1);
                this._syncFilesToInput();
            },

            _syncFilesToInput() {
                const input = document.getElementById('masterFileInput');
                if (!input) return;
                try {
                    const dt = new DataTransfer();
                    this.selectedFiles.forEach(f => dt.items.add(f));
                    input.files = dt.files;
                } catch(e) { 
                    console.error("Sync failed:", e);
                }
            },

            async deleteImage(id) {
                if (!confirm('Hapus gambar ini?')) return;
                try {
                    const res = await fetch(`{{ url('guru/portofolio/image') }}/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    const result = await res.json();
                    if (result.success) this.existingImages = this.existingImages.filter(i => i.id_portofolio_images !== id);
                    else alert(result.message || 'Gagal menghapus');
                } catch (e) { alert('Gagal menghapus gambar'); }
            }
        }
    }
</script>
@endpush

@endsection
