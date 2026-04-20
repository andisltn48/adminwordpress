<x-app-layout>
    <div class="py-12" x-data="beritaForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-8">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="font-bold text-4xl text-slate-800 tracking-tight">
                        {{ __('Tambah Berita Baru') }}
                    </h2>
                    <p class="mt-2 text-slate-500">
                        {{ __('Buat satu atau lebih berita sekaligus dan pilih website target.') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showPreview = true"
                        class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 rounded-2xl font-bold text-sm text-slate-700 hover:bg-slate-50 active:scale-95 transition-all duration-300 shadow-sm whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-slate-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ __('Preview') }}
                    </button>
                    <a href="{{ route('beritas.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 rounded-2xl font-bold text-sm text-slate-600 hover:bg-slate-50 active:scale-95 transition-all duration-300 shadow-sm whitespace-nowrap">
                        {{ __('Batal') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('beritas.store') }}" method="POST" enctype="multipart/form-data" id="main-form">
                @csrf
                <div class="max-w-7xl mx-auto mb-8" x-show="showErrors">
                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-2xl shadow-sm mb-6 relative">
                            <button type="button" @click="showErrors = false"
                                class="absolute top-2 right-2 text-red-400 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-red-800">Terdapat kesalahan pada input:</h3>
                                    <ul
                                        class="mt-1 text-sm text-red-700 list-disc list-inside max-h-32 overflow-y-auto">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <template x-for="(item, index) in newsItems" :key="item.id">
                    <div class="premium-card mb-8 relative group">
                        <!-- Add/Remove Badge -->
                        <div class="absolute -left-3 top-6 flex flex-col gap-2">
                            <div class="bg-primary-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold shadow-lg shadow-primary-500/30 z-10"
                                x-text="index + 1"></div>
                            <button type="button" x-show="newsItems.length > 1" @click="removeNews(index)"
                                class="bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold shadow-lg shadow-red-500/30 z-10 hover:bg-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M20 12H4" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-8">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                <!-- Left Column: Title & Content -->
                                <div class="lg:col-span-2 space-y-6">
                                    <div>
                                        <label :for="'judul_' + item.id"
                                            class="block font-bold text-sm text-slate-700 mb-2 ml-1">
                                            {{ __('Judul Berita') }}
                                        </label>
                                        <input :id="'judul_' + item.id" :name="'news[' + index + '][judul]'"
                                            type="text" x-model="item.judul"
                                            class="block w-full border-slate-200 focus:border-primary-500 focus:ring-primary-500 rounded-2xl shadow-sm transition-all duration-300 py-3 px-4"
                                            placeholder="Masukkan judul berita yang menarik..." required />
                                    </div>

                                    <div>
                                        <label :for="'konten_' + item.id"
                                            class="block font-bold text-sm text-slate-700 mb-2 ml-1">
                                            {{ __('Konten Berita') }}
                                        </label>
                                        <div
                                            class="ckeditor-container rounded-2xl overflow-hidden border border-slate-200">
                                            <textarea :id="'konten_' + item.id" :name="'news[' + index + '][konten]'" class="ck-editor-area" x-model="item.konten"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Image & Websites -->
                                <div class="space-y-6">
                                    <div>
                                        <x-input-label :value="__('Featured Image')" class="text-slate-700 font-bold mb-2 ml-1" />
                                        <div class="relative group/img">
                                            <label
                                                class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-slate-200 rounded-3xl cursor-pointer bg-slate-50/50 hover:bg-slate-50 hover:border-primary-300 transition-all duration-300 overflow-hidden">
                                                <template x-if="!item.imagePreview">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                        <div
                                                            class="p-3 bg-white rounded-2xl shadow-sm mb-3 group-hover/img:scale-110 transition-transform duration-300">
                                                            <svg class="w-8 h-8 text-primary-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </div>
                                                        <p class="text-xs text-slate-500 font-medium">PNG, JPG up to
                                                            2MB
                                                        </p>
                                                    </div>
                                                </template>
                                                <template x-if="item.imagePreview">
                                                    <img :src="item.imagePreview" class="w-full h-full object-cover">
                                                </template>
                                                <input type="file" :name="'news[' + index + '][featured_image]'"
                                                    class="hidden" @change="handleImageUpload($event, index)"
                                                    accept="image/*" />
                                            </label>
                                            <template x-if="item.imagePreview">
                                                <button type="button" @click="removeImage(index)"
                                                    class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full shadow-lg opacity-0 group-hover/img:opacity-100 transition-opacity">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <div>
                                        <x-input-label :value="__('Pilih Website')" class="text-slate-700 font-bold mb-2 ml-1" />
                                        <div
                                            class="bg-slate-50 rounded-2xl p-4 border border-slate-100 max-h-60 overflow-y-auto space-y-2">
                                            @foreach ($websites as $web)
                                                <label class="flex items-center group/check cursor-pointer">
                                                    <input type="checkbox" :name="'news[' + index + '][website_ids][]'"
                                                        value="{{ $web->id }}" x-model="item.website_ids"
                                                        class="w-5 h-5 flex-shrink-0 text-primary-600 border-slate-200 rounded-lg focus:ring-primary-500 transition-all duration-300">
                                                    <div class="ml-3">
                                                        <span
                                                            class="block text-sm font-bold text-slate-700 group-hover/check:text-primary-600 transition-colors">{{ $web->nama_website }}</span>
                                                        <span
                                                            class="block text-[10px] text-slate-400 font-medium tracking-wider">{{ $web->url }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <x-input-label :value="__('Kategori')" class="text-slate-700 font-bold mb-2 ml-1" />
                                        <input type="text" :name="'news[' + index + '][kategori]'" x-model="item.kategori"
                                            class="block w-full border-slate-200 focus:border-primary-500 focus:ring-primary-500 rounded-2xl shadow-sm transition-all duration-300 py-3 px-4 text-sm"
                                            placeholder="Contoh: Berita, Pengumuman, Artikel..." />
                                    </div>

                                    <div>
                                        <x-input-label :value="__('Tanggal Publikasi')" class="text-slate-700 font-bold mb-2 ml-1" />
                                        <input type="date" :name="'news[' + index + '][tanggal_publikasi]'" x-model="item.tanggal_publikasi"
                                            class="block w-full border-slate-200 focus:border-primary-500 focus:ring-primary-500 rounded-2xl shadow-sm transition-all duration-300 py-3 px-4 text-sm" />
                                    </div>

                                    <div>
                                        <x-input-label :value="__('Status')" class="text-slate-700 font-bold mb-2 ml-1" />
                                        <div class="flex gap-2 p-1 bg-slate-100 rounded-2xl w-full">
                                            <button type="button" @click="item.status = 'Draft'"
                                                :class="item.status === 'Draft' ? 'bg-white text-slate-800 shadow-sm' :
                                                    'text-slate-500 hover:text-slate-700'"
                                                class="flex-1 py-2 px-4 rounded-xl text-xs font-bold transition-all duration-300">
                                                Draft
                                            </button>
                                            <button type="button" @click="item.status = 'Published'"
                                                :class="item.status === 'Published' ?
                                                    'bg-emerald-500 text-white shadow-lg shadow-emerald-500/25' :
                                                    'text-slate-500 hover:text-slate-700'"
                                                class="flex-1 py-2 px-4 rounded-xl text-xs font-bold transition-all duration-300">
                                                Published
                                            </button>
                                            <input type="hidden" :name="'news[' + index + '][status]'"
                                                x-model="item.status">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Add More Button -->
                <div class="flex justify-center mb-12">
                    <button type="button" @click="addNews()"
                        class="group flex items-center gap-3 px-8 py-4 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-slate-400 hover:border-primary-400 hover:text-primary-600 transition-all duration-300 active:scale-95 shadow-sm">
                        <div class="p-2 bg-slate-50 rounded-xl group-hover:bg-primary-50 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <span class="font-bold tracking-tight text-lg">{{ __('Tambah Berita Lainnya') }}</span>
                    </button>
                </div>

                <!-- Sticky Action Bar -->
                <div class="fixed bottom-8 left-1/2 -translate-x-1/2 w-full max-w-lg z-[55] px-6">
                    <div
                        class="bg-slate-900/90 backdrop-blur-xl border border-white/10 p-3 rounded-3xl shadow-2xl flex items-center justify-between gap-4">
                        <div class="flex items-center ml-4">
                            <span class="text-white/60 text-sm font-medium">Total:</span>
                            <span class="ml-2 text-white font-bold bg-primary-600 px-3 py-0.5 rounded-full text-sm"
                                x-text="newsItems.length"></span>
                        </div>
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center px-8 py-4 bg-primary-500 border border-transparent rounded-2xl font-bold text-base text-white hover:bg-primary-400 active:scale-95 transition-all duration-300 shadow-xl shadow-primary-500/25">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Simpan Semua Berita') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Modal -->
        <div x-show="showPreview" class="fixed inset-0 z-[60] overflow-y-auto"
            @keydown.escape.window="showPreview = false" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showPreview" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-slate-900/80 backdrop-blur-sm"
                    @click="showPreview = false"></div>

                <div x-show="showPreview" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block w-full max-w-5xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl">
                    <div
                        class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center sticky top-0 z-10">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-800">Preview Rangkuman Berita</h3>
                            <p class="text-sm text-slate-500">Tinjau konten sebelum dipublish.</p>
                        </div>
                        <button @click="showPreview = false"
                            class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 rounded-xl transition-all">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-8 space-y-8 max-h-[70vh] overflow-y-auto">
                        <template x-for="(item, index) in newsItems" :key="item.id">
                            <div class="border border-slate-100 rounded-3xl p-6 bg-slate-50/30">
                                <div class="flex gap-6">
                                    <div
                                        class="w-32 h-32 flex-shrink-0 bg-slate-100 rounded-2xl overflow-hidden border border-slate-200">
                                        <template x-if="item.imagePreview">
                                            <img :src="item.imagePreview" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!item.imagePreview">
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="text-xl font-bold text-slate-800"
                                                x-text="item.judul || 'Tanpa Judul'"></h4>
                                            <span
                                                :class="item.status === 'Published' ? 'bg-emerald-100 text-emerald-700' :
                                                    'bg-slate-100 text-slate-600'"
                                                class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full"
                                                x-text="item.status"></span>
                                        </div>
                                        <div class="text-sm text-slate-600 line-clamp-2 prose prose-sm max-w-none mb-4"
                                            x-html="item.konten || '<span class=\'italic text-slate-400\'>Tidak ada konten</span>'">
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="webId in item.website_ids" :key="webId">
                                                <span
                                                    class="px-3 py-1 bg-primary-50 text-primary-700 text-[10px] font-bold rounded-lg border border-primary-100"
                                                    x-text="getWebsiteName(webId)"></span>
                                            </template>
                                            <template x-if="!item.website_ids || item.website_ids.length === 0">
                                                <span class="text-[10px] text-red-500 font-bold italic">Belum pilih
                                                    website target</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-end">
                        <button @click="showPreview = false"
                            class="px-8 py-3 bg-slate-800 text-white font-bold rounded-2xl hover:bg-slate-700 transition-all">
                            Tutup Preview
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CKEditor 5 -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <style>
        .ck-editor__editable {
            min-height: 200px;
        }

        .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
            border-color: transparent;
        }

        .ck.ck-editor__top .ck-sticky-panel .ck-toolbar {
            border-top: none;
            border-left: none;
            border-right: none;
            background: #f8fafc;
            padding: 8px;
        }
    </style>

    <script>
        function beritaForm() {
            // Keep editors in a non-reactive object to avoid Proxy errors
            let editors = {};

            // Custom Upload Adapter for CKEditor
            class MyUploadAdapter {
                constructor(loader) {
                    this.loader = loader;
                }

                upload() {
                    return this.loader.file
                        .then(file => new Promise((resolve, reject) => {
                            this._initRequest();
                            this._initListeners(resolve, reject, file);
                            this._sendRequest(file);
                        }));
                }

                abort() {
                    if (this.xhr) {
                        this.xhr.abort();
                    }
                }

                _initRequest() {
                    const xhr = this.xhr = new XMLHttpRequest();
                    xhr.open('POST', "{{ route('beritas.upload_image') }}", true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', "{{ csrf_token() }}");
                    xhr.responseType = 'json';
                }

                _initListeners(resolve, reject, file) {
                    const xhr = this.xhr;
                    const loader = this.loader;
                    const genericErrorText = `Couldn't upload file: ${file.name}.`;

                    xhr.addEventListener('error', () => reject(genericErrorText));
                    xhr.addEventListener('abort', () => reject());
                    xhr.addEventListener('load', () => {
                        const response = xhr.response;
                        if (!response || response.error) {
                            return reject(response && response.error ? response.error.message :
                                genericErrorText);
                        }
                        resolve({
                            default: response.url
                        });
                    });

                    if (xhr.upload) {
                        xhr.upload.addEventListener('progress', evt => {
                            if (evt.lengthComputable) {
                                loader.uploadTotal = evt.total;
                                loader.uploaded = evt.loaded;
                            }
                        });
                    }
                }

                _sendRequest(file) {
                    const data = new FormData();
                    data.append('upload', file);
                    this.xhr.send(data);
                }
            }

            function MyCustomUploadAdapterPlugin(editor) {
                editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                    return new MyUploadAdapter(loader);
                };
            }

            return {
                newsItems: (@json(old('news')) || []).map((item, index) => ({
                    id: item.id || Date.now() + index,
                    judul: item.judul || '',
                    konten: item.konten || '',
                    website_ids: item.website_ids || [],
                    status: item.status || 'Published',
                    kategori: item.kategori || '',
                    tanggal_publikasi: item.tanggal_publikasi || '',
                    imagePreview: null // Browser security prevents re-loading local file paths
                })),
                showPreview: false,
                showErrors: true,
                websites: @json($websites),

                init() {
                    // Start with one news item if empty
                    if (this.newsItems.length === 0) {
                        this.addNews();
                    } else {
                        // If we have items (e.g. from old()), initialize them
                        // But wrap in $nextTick to ensure DOM is ready
                        this.$nextTick(() => {
                            this.newsItems.forEach((item) => {
                                this.initEditor(item.id);
                            });
                        });
                    }
                },

                addNews() {
                    const id = Date.now();
                    this.newsItems.push({
                        id: id,
                        judul: '',
                        konten: '',
                        website_ids: [],
                        status: 'Published',
                        kategori: '',
                        tanggal_publikasi: '',
                        imagePreview: null
                    });
                    this.$nextTick(() => {
                        this.initEditor(id);
                    });
                },

                removeNews(index) {
                    if (this.newsItems.length > 1) {
                        const item = this.newsItems[index];
                        const editorId = 'konten_' + item.id;

                        if (editors[editorId]) {
                            editors[editorId].destroy();
                            delete editors[editorId];
                        }
                        this.newsItems.splice(index, 1);
                    }
                },

                initEditor(id) {
                    // Ensure we don't initialize the same editor twice
                    if (editors[id]) return;

                    const editorElement = document.querySelector('#konten_' + id);
                    if (!editorElement) return;

                    // Clean up any existing editor instance on the element just in case
                    if (editorElement.nextSibling && editorElement.nextSibling.classList && editorElement.nextSibling
                        .classList.contains('ck-editor')) {
                        editorElement.nextSibling.remove();
                    }
                    ClassicEditor
                        .create(editorElement, {
                            toolbar: [
                                'heading', '|',
                                'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                                'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', '|',
                                'undo', 'redo'
                            ],
                            extraPlugins: [MyCustomUploadAdapterPlugin],
                        })
                        .then(editor => {
                            editors[id] = editor;

                            // Sync initial content
                            this.newsItems.find(item => item.id == id).konten = editor.getData();

                            editor.model.document.on('change:data', () => {
                                const data = editor.getData();
                                const item = this.newsItems.find(item => item.id == id);
                                if (item) {
                                    item.konten = data;
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error initializing editor:', error);
                        });
                },

                handleImageUpload(event, index) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.newsItems[index].imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                removeImage(index) {
                    this.newsItems[index].imagePreview = null;
                    const input = document.querySelector(`input[name="news[${index}][featured_image]"]`);
                    if (input) input.value = '';
                },

                getWebsiteName(id) {
                    const web = this.websites.find(w => w.id == id);
                    return web ? web.nama_website : '';
                }
            };
        }
    </script>
</x-app-layout>
