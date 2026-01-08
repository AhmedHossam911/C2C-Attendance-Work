@if (session()->has('success') ||
        session()->has('error') ||
        session()->has('warning') ||
        session()->has('info') ||
        $errors->any())
    <div x-data="{
        show: true,
        init() {
            setTimeout(() => this.show = false, 5000);
        }
    }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed top-20 right-4 z-50 flex flex-col gap-2 w-full max-w-sm"
        role="alert">

        @if (session('success'))
            <div
                class="px-4 py-3 rounded-xl bg-green-500 text-white shadow-lg flex items-center gap-3 border border-green-600/20">
                <i
                    class="bi bi-check-circle-fill text-xl bg-white/20 rounded-full h-8 w-8 flex items-center justify-center"></i>
                <div class="flex-1">
                    <p class="font-bold text-sm">Success</p>
                    <p class="text-sm opacity-90">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-white/70 hover:text-white transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div
                class="px-4 py-3 rounded-xl bg-red-500 text-white shadow-lg flex items-center gap-3 border border-red-600/20">
                <i
                    class="bi bi-exclamation-circle-fill text-xl bg-white/20 rounded-full h-8 w-8 flex items-center justify-center"></i>
                <div class="flex-1">
                    <p class="font-bold text-sm">Error</p>
                    <p class="text-sm opacity-90">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-white/70 hover:text-white transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        @if (session('warning'))
            <div
                class="px-4 py-3 rounded-xl bg-yellow-500 text-white shadow-lg flex items-center gap-3 border border-yellow-600/20">
                <i
                    class="bi bi-exclamation-triangle-fill text-xl bg-white/20 rounded-full h-8 w-8 flex items-center justify-center"></i>
                <div class="flex-1">
                    <p class="font-bold text-sm">Warning</p>
                    <p class="text-sm opacity-90">{{ session('warning') }}</p>
                </div>
                <button @click="show = false" class="text-white/70 hover:text-white transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        @if (session('info'))
            <div
                class="px-4 py-3 rounded-xl bg-blue-500 text-white shadow-lg flex items-center gap-3 border border-blue-600/20">
                <i
                    class="bi bi-info-circle-fill text-xl bg-white/20 rounded-full h-8 w-8 flex items-center justify-center"></i>
                <div class="flex-1">
                    <p class="font-bold text-sm">Info</p>
                    <p class="text-sm opacity-90">{{ session('info') }}</p>
                </div>
                <button @click="show = false" class="text-white/70 hover:text-white transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div
                class="px-4 py-3 rounded-xl bg-red-500 text-white shadow-lg flex items-center gap-3 border border-red-600/20">
                <i
                    class="bi bi-x-octagon-fill text-xl bg-white/20 rounded-full h-8 w-8 flex items-center justify-center"></i>
                <div class="flex-1">
                    <p class="font-bold text-sm">There were some errors</p>
                    <ul class="list-disc pl-4 text-sm opacity-90 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-white/70 hover:text-white transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif
    </div>
@endif
