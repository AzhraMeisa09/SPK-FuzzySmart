{{-- ══ MODAL (x-teleport ke body) ══ --}}
<template x-teleport="body">
    <div x-show="formOpen" @click.self="closeForm()"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6"
         style="display:none; background: rgba(9, 9, 11, 0.72); backdrop-filter: blur(8px);">
        
        <div x-show="formOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="card flex flex-col overflow-hidden w-full max-w-2xl max-h-[calc(100svh-2rem)] bg-white" style="border: 1px solid var(--border);">

            {{-- HEADER --}}
            <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--border); background: var(--bg);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 00-2-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold" style="color: var(--text-1);" x-text="isEdit ? 'Update portofolio' : 'Tambah portofolio'"></h3>
                        <p class="text-[10px]" style="color: var(--text-3);">Dokumentasi perkembangan siswa</p>
                    </div>
                </div>
                <button @click="closeForm()" type="button" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors" style="color: var(--text-3);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- BODY --}}
            <div class="p-6 overflow-y-auto flex-1">
                <form :action="isEdit ? '{{ url('guru/portofolio') }}/' + editId : '{{ route('guru.portofolio.store') }}'"
                      method="POST" enctype="multipart/form-data" id="portofolioForm" class="space-y-5"
                      @submit.prevent="submitForm()">
                    @csrf
                    <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                    
                    {{-- Master File Input (All files synced here) --}}
                    <input type="file" name="images[]" multiple class="hidden" id="masterFileInput">

                    {{-- Siswa & Minggu --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group !mb-0">
                            <label class="form-label">Siswa</label>
                            <select name="siswa_id" x-model="formData.siswa_id" required class="form-input">
                                <option value="">— Pilih Siswa —</option>
                                @foreach($siswa->groupBy('kelas_id') as $kelasId => $siswaGroup)
                                    <optgroup label="KELAS {{ $siswaGroup->first()->kelas->nama_kelas }}">
                                        @foreach($siswaGroup as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group !mb-0">
                            <label class="form-label">Minggu Penilaian</label>
                            <select name="minggu_id" x-model="formData.minggu_id" required class="form-input">
                                <option value="">— Pilih Minggu —</option>
                                @foreach($minggu as $m)
                                    <option value="{{ $m->id }}">Minggu {{ $m->minggu_ke }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Judul --}}
                    <div class="form-group !mb-0">
                        <label class="form-label">Judul Kegiatan</label>
                        <input type="text" name="judul" x-model="formData.judul" required
                               class="form-input" placeholder="Contoh: Kolase Daun dan Bunga">
                    </div>

                    {{-- Deskripsi --}}
                    <div class="form-group !mb-0">
                        <label class="form-label">Catatan Guru</label>
                        <textarea name="deskripsi" x-model="formData.deskripsi" rows="3"
                                  class="form-input resize-y min-h-[80px]" placeholder="Tuliskan observasi dan pencapaian siswa..."></textarea>
                    </div>

                    {{-- Live Camera Preview --}}
                    <div x-show="showCamera" x-cloak class="relative rounded-2xl overflow-hidden bg-black aspect-video mb-5 border-4 border-var(--accent)/20 group/cam">
                        <video id="cameraVideo" x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                        
                        {{-- Flash Effect --}}
                        <div x-show="flash" x-transition.opacity.duration.150ms class="absolute inset-0 bg-white z-[10]"></div>
                        <div class="absolute inset-x-0 bottom-0 p-4 flex items-center justify-center gap-4 bg-gradient-to-t from-black/80 to-transparent">
                            <button type="button" @click="stopCamera()" class="w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/30 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            
                            <button type="button" @click="takePhoto()" class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-lg hover:scale-110 active:scale-95 transition-all">
                                <div class="w-10 h-10 rounded-full border-4 border-black/10"></div>
                            </button>

                            <button type="button" @click="switchCamera()" class="w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/30 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Foto --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="form-label !mb-0">Lampiran Foto</label>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest" x-show="!showCamera">Klik "Ambil Foto" untuk kamera langsung</span>
                        </div>
                        
                        <div class="grid grid-cols-4 sm:grid-cols-5 gap-3 mt-2" id="photoGrid">
                            {{-- Camera button (Toggle Live Preview) --}}
                            <button type="button" @click="startCamera()" 
                                    class="aspect-square rounded-xl flex flex-col items-center justify-center cursor-pointer transition-all group/cam" 
                                    :style="showCamera ? 'border: 2px solid var(--accent); background: var(--accent-lt);' : 'border: 2px dashed var(--accent); background: var(--accent-lt);'">
                                <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center transition-all group-hover/cam:scale-110" style="color: var(--accent);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <span class="text-[8px] font-black uppercase mt-2 tracking-widest" style="color: var(--accent);" x-text="showCamera ? 'Sedang Aktif' : 'Ambil Foto'"></span>
                            </button>

                            {{-- Existing images (edit mode) --}}
                            <template x-for="img in existingImages" :key="img.id">
                                <div class="relative aspect-square rounded-xl overflow-hidden shadow-sm group/img" style="border: 1px solid var(--border);">
                                    <img :src="'{{ asset('storage') }}/' + img.file_path" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover/img:opacity-100 transition-all flex items-center justify-center">
                                        <button type="button" @click="deleteImage(img.id)"
                                                class="bg-rose-500 text-white p-2 rounded-lg shadow scale-75 group-hover/img:scale-100 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            {{-- NEW: Preview foto baru yang dipilih --}}
                            <template x-for="(preview, idx) in newImages" :key="idx">
                                <div class="relative aspect-square rounded-xl overflow-hidden shadow-sm group/prev" style="border: 2px solid var(--accent);">
                                    <img :src="preview.url" class="w-full h-full object-cover">
                                    <div class="absolute top-1 left-1 text-white text-[7px] font-black uppercase px-1.5 py-0.5 rounded-md tracking-wider" style="background: var(--accent);">Baru</div>
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover/prev:opacity-100 transition-all flex items-center justify-center">
                                        <button type="button" @click="removePreview(idx)"
                                                class="bg-rose-500 text-white p-2 rounded-lg shadow scale-75 group-hover/prev:scale-100 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            {{-- Upload button (Gallery) --}}
                            <label class="aspect-square rounded-xl flex flex-col items-center justify-center cursor-pointer transition-all group/up" style="border: 2px dashed var(--border); background: var(--bg);">
                                <input type="file" multiple accept="image/*" class="hidden" x-ref="fileInput" @change="handleFileSelect">
                                <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center transition-all group-hover/up:scale-110" style="color: var(--text-3);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 00-2-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-[8px] font-bold uppercase mt-2 tracking-widest" style="color: var(--text-3);">Galeri</span>
                            </label>
                        </div>
                        {{-- Info jumlah foto baru & Status Kompresi --}}
                        <div class="flex items-center justify-between mt-2 ml-1">
                            <p x-show="newImages.length > 0" class="text-[10px] font-bold" style="color: var(--accent);">
                                <span x-text="newImages.length"></span> foto baru dipilih &bull; akan diupload saat disimpan
                            </p>
                            <div x-show="isProcessing" class="flex items-center gap-1.5 text-[10px] font-bold text-amber-500 animate-pulse">
                                <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Mengompresi...
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 flex flex-col gap-3" style="border-top: 1px solid var(--border); background: var(--bg);">
                {{-- Progress Bar --}}
                <div x-show="submitting" x-cloak class="w-full">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--accent);">Mengirim Data...</span>
                        <span class="text-[10px] font-bold" style="color: var(--accent);" x-text="uploadProgress + '%'"></span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 transition-all duration-300" :style="'width: ' + uploadProgress + '%'"></div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="closeForm()" class="btn btn-gray px-6 py-2" :disabled="submitting">Batal</button>
                    <button type="submit" form="portofolioForm" class="btn btn-green px-8 py-2 relative overflow-hidden" 
                            :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                            :disabled="submitting || isProcessing">
                        <span x-show="!submitting" x-text="isEdit ? 'Perbarui' : 'Simpan'"></span>
                        <span x-show="submitting" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            Mengirim...
                        </span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</template>
