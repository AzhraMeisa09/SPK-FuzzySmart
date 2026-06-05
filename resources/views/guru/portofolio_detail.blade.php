@extends('layouts.app')
@section('title', 'Detail Portofolio: ' . $portofolio->judul)
@section('page-title', 'Detail portofolio')

@section('content')
<div class="space-y-5 pb-10 fade-in" x-data="portofolioModule()">

    {{-- ── HERO HEADER ── --}}
    <div class="card p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: var(--accent-lt); color: var(--accent);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 00-2-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="badge badge-blue px-2 py-0.5 text-[9px]">Minggu {{ $portofolio->minggu->minggu_ke }}</span>
                        <div class="w-1 h-1 rounded-full" style="background: var(--border);"></div>
                        <span class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">{{ $portofolio->siswa->nama }}</span>
                    </div>
                    <h1 class="text-base font-semibold leading-tight" style="color: var(--text-1);">{{ $portofolio->judul }}</h1>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('guru.portofolio.index') }}" class="btn btn-gray btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
                
                @if($portofolio->minggu->periode->status !== 'final')
                    <button @click="openModal({{ json_encode($portofolio->load('images')) }})" 
                            class="btn btn-green btn-sm">
                        Edit dokumen
                    </button>
                    <form action="{{ route('guru.portofolio.destroy', $portofolio->id_portofolio) }}" method="POST" onsubmit="return confirm('Hapus portofolio ini secara permanen?')" class="inline-block">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm hover:bg-rose-50 hover:text-rose-600 transition-colors" style="border: 1px solid var(--border); color: var(--text-2);">
                            Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        {{-- GALLERY SECTION --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-5 pb-4" style="border-bottom: 1px solid var(--border);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 00-2-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold" style="color: var(--text-1);">Dokumentasi foto</h3>
                        <p class="text-[10px]" style="color: var(--text-3);">{{ $portofolio->images->count() }} gambar terlampir</p>
                    </div>
                </div>

                @if($portofolio->images->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($portofolio->images as $img)
                            <div class="relative group aspect-[4/3] rounded-2xl overflow-hidden shadow-sm cursor-pointer" 
                                 style="background: var(--bg); border: 1px solid var(--border);"
                                 @click="$dispatch('open-lightbox', '{{ asset('storage/' . $img->file_path) }}')">
                                <img src="{{ asset('storage/' . $img->file_path) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all flex items-center justify-center">
                                    <div class="w-10 h-10 rounded-full bg-white/90 shadow-lg flex items-center justify-center scale-0 group-hover:scale-100 transition-transform text-var(--accent)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-16 text-center rounded-2xl" style="background: var(--bg); border: 1px dashed var(--border);">
                        <p class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">Tidak ada foto dokumentasi</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- SIDEBAR INFO --}}
        <div class="space-y-5">
            {{-- Catatan Guru --}}
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-4 pb-3" style="border-bottom: 1px solid var(--border);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold" style="color: var(--text-1);">Catatan guru</h3>
                </div>
                
                <div class="p-4 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                    <p class="text-xs font-medium italic leading-relaxed" style="color: var(--text-2); white-space: pre-wrap;">&ldquo;{{ $portofolio->deskripsi ?: 'Tidak ada deskripsi tambahan untuk dokumentasi ini.' }}&rdquo;</p>
                </div>
                
                <div class="mt-5 space-y-3 px-1">
                    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">
                        <span>Tanggal input</span>
                        <span style="color: var(--text-1);">{{ $portofolio->created_at->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">
                        <span>Siswa</span>
                        <a href="{{ route('guru.siswa.show', $portofolio->siswa_id) }}" style="color: var(--accent);" class="hover:underline">{{ $portofolio->siswa->nama }}</a>
                    </div>
                </div>
            </div>

            {{-- Context Card --}}
            <div class="card p-5" style="border: 1px solid var(--accent); background: var(--accent-lt);">
                <h4 class="text-[10px] font-bold uppercase tracking-widest mb-3" style="color: var(--accent);">Informasi periode</h4>
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full" style="background: var(--accent);"></div>
                    <div>
                        <span class="block text-[9px] font-bold uppercase tracking-widest" style="color: var(--accent); opacity: 0.8;">Semester / Tahun</span>
                        <p class="text-sm font-bold mt-0.5" style="color: var(--accent);">{{ $portofolio->minggu->periode->nama_periode }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include the modal partial --}}
    @include('guru.portofolio_modal')

    {{-- ── LIGHTBOX MODAL ── --}}
    <div x-data="{ isOpen: false, imgUrl: '' }" 
         @open-lightbox.window="isOpen = true; imgUrl = $event.detail"
         x-show="isOpen" 
         x-cloak
         class="fixed inset-0 z-[10000] flex items-center justify-center p-6"
         style="background: rgba(9, 9, 11, 0.72); backdrop-filter: blur(8px);"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <button @click="isOpen = false" class="absolute top-8 right-8 w-12 h-12 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20 transition-all border border-white/10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="relative max-w-5xl w-full max-h-[85vh] flex items-center justify-center">
            <img :src="imgUrl" @click.away="isOpen = false" class="max-w-full max-h-full object-contain rounded-3xl shadow-[0_0_50px_rgba(0,0,0,0.5)] border-4 border-white/10">
        </div>
    </div>

</div>

@push('scripts')
<script>
    function portofolioModule() {
        return {
            formOpen: false,
            isEdit: false,
            editId: null,
            formData: { siswa_id: '', minggu_id: '', judul: '', deskripsi: '' },
            existingImages: [],
            newImages: [],      // { url, file } untuk preview sebelum upload
            selectedFiles: [],  // File[] aktual untuk dikirim
            
            // UX States
            isProcessing: false, // Sedang kompresi gambar
            submitting: false,   // Sedang upload ke server
            uploadProgress: 0,   // Persentase upload

            // Camera Logic (Needed because modal uses it)
            showCamera: false,
            cameraStream: null,
            currentFacingMode: 'environment',
            flash: false,

            openModal(data = null) {
                if (data) {
                    this.isEdit = true;
                    this.editId = data.id_portofolio;
                    this.formData = { 
                        siswa_id: data.siswa_id, 
                        minggu_id: data.minggu_id, 
                        judul: data.judul, 
                        deskripsi: data.deskripsi 
                    };
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
                
                const xhr = new XMLHttpRequest();
                const url = this.isEdit ? `{{ url('guru/portofolio') }}/${this.editId}` : `{{ route('guru.portofolio.store') }}`;
                
                xhr.open('POST', url, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable) {
                        this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                    }
                };

                xhr.onload = () => {
                    let res;
                    try { res = JSON.parse(xhr.responseText); } catch(e) {
                        res = { success: false, message: 'Server error: Terjadi kesalahan pada sistem.' };
                    }

                    if (xhr.status >= 200 && xhr.status < 300 && res.success) {
                        window.location.href = res.redirect || '{{ route('guru.portofolio.index') }}';
                    } else {
                        this.submitting = false;
                        alert(res.message || 'Gagal menyimpan data.');
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
                } catch(e) { }
            },

            async deleteImage(id) {
                if (!confirm('Hapus gambar ini secara permanen?')) return;
                try {
                    const res = await fetch(`{{ url('guru/portofolio/image') }}/${id}`, {
                        method: 'DELETE',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const result = await res.json();
                    if (result.success) {
                        this.existingImages = this.existingImages.filter(img => img.id_portofolio_images !== id);
                    } else {
                        alert(result.message || 'Gagal menghapus gambar');
                    }
                } catch (e) { alert('Gagal menghapus gambar'); }
            }
        }
    }
</script>
@endpush
@endsection
