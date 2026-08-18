<x-layouts::admin :title="'Announcements'">
    <div x-data="{ adding: false }">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Announcements</h1>
            <x-button size="sm" @click="adding = !adding"><x-icon name="plus" class="h-4 w-4" /> New Announcement</x-button>
        </div>

        <form method="POST" action="{{ route('admin.announcements.store') }}" x-show="adding" x-cloak class="card card-body mt-4 space-y-4">
            @csrf
            <x-input label="Title" name="title" required />
            <x-textarea label="Message" name="body" rows="4" required />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-select label="Audience" name="audience" required :options="['all' => 'Everyone', 'alumni' => 'Alumni Only', 'admins' => 'Admin Staff Only']" />
                <div>
                    <label class="form-label">Expires On (optional)</label>
                    <input type="text" name="expires_at" class="form-input flatpickr-expiry" autocomplete="off">
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                <input type="checkbox" name="is_pinned" value="1" class="rounded border-slate-300 text-navy-700 focus:ring-navy-500"> Pin to top
            </label>
            <div class="flex justify-end"><x-button type="submit" size="sm">Publish &amp; Notify</x-button></div>
        </form>

        @if ($announcements->isEmpty())
            <x-empty-state icon="megaphone" title="No announcements yet" class="mt-8" />
        @else
            <div class="mt-6 space-y-3">
                @foreach ($announcements as $announcement)
                    <div class="card card-body" x-data="{ editing: false }">
                        <div class="flex items-start justify-between gap-4">
                            <div x-show="!editing">
                                <p class="flex items-center gap-1.5 font-semibold text-slate-900 dark:text-white">
                                    @if ($announcement->is_pinned)<x-icon name="megaphone" class="h-4 w-4 text-gold-500" />@endif
                                    {{ $announcement->title }}
                                </p>
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $announcement->body }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ ucfirst($announcement->audience) }} &middot; {{ $announcement->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex flex-shrink-0 items-center gap-2">
                                <button type="button" @click="editing = !editing" class="text-slate-400 hover:text-navy-700" title="Edit"><x-icon name="pencil" class="h-4 w-4" /></button>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Delete this announcement?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Delete'}).then(r=>r.isConfirmed&&this.submit())">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600" title="Delete"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                </form>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" x-show="editing" x-cloak class="mt-4 space-y-4">
                            @csrf @method('PUT')
                            <x-input label="Title" name="title" required :value="$announcement->title" />
                            <x-textarea label="Message" name="body" rows="4" required>{{ $announcement->body }}</x-textarea>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <x-select label="Audience" name="audience" required :options="['all' => 'Everyone', 'alumni' => 'Alumni Only', 'admins' => 'Admin Staff Only']" :selected="$announcement->audience" />
                                <div>
                                    <label class="form-label">Expires On (optional)</label>
                                    <input type="text" name="expires_at" value="{{ $announcement->expires_at?->format('Y-m-d H:i') }}" class="form-input flatpickr-expiry" autocomplete="off">
                                </div>
                            </div>
                            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                <input type="checkbox" name="is_pinned" value="1" @checked($announcement->is_pinned) class="rounded border-slate-300 text-navy-700 focus:ring-navy-500"> Pin to top
                            </label>
                            <div class="flex justify-end gap-2">
                                <x-button type="button" variant="secondary" size="sm" @click="editing = false">Cancel</x-button>
                                <x-button type="submit" size="sm">Save Changes</x-button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $announcements->links() }}</div>
        @endif
    </div>

    @push('scripts')
    <script>document.addEventListener('DOMContentLoaded', () => flatpickr('.flatpickr-expiry', { enableTime: true, dateFormat: 'Y-m-d H:i' }));</script>
    @endpush
</x-layouts::admin>
