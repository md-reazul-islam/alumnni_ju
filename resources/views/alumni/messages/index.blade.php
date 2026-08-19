<x-layouts::alumni :title="'Messages'">
    <div class="-mx-4 -my-8 flex h-[calc(100vh-4rem)] overflow-hidden sm:mx-0 sm:my-0 sm:h-[calc(100vh-10rem)] sm:rounded-2xl sm:border sm:border-slate-200 dark:sm:border-navy-800">
        {{-- Conversation list --}}
        <div class="flex w-full flex-shrink-0 flex-col border-r border-slate-200 bg-white dark:border-navy-800 dark:bg-navy-900 sm:w-80 {{ $activeConversation ? 'hidden sm:flex' : 'flex' }}">
            <div class="border-b border-slate-100 p-4 dark:border-navy-800">
                <h1 class="text-lg font-bold text-slate-900 dark:text-white">Messages</h1>
                <form method="GET" action="{{ route('messages.index') }}" class="relative mt-3">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search conversations" class="form-input pl-9">
                </form>
            </div>

            <div class="flex-1 overflow-y-auto" id="conversation-list">
                @if ($conversations->isEmpty())
                    <div class="p-6">
                        <x-empty-state icon="message-circle" title="No conversations yet" description="Start a conversation from any alumni profile." />
                    </div>
                @else
                    @if ($mentorConversations->isNotEmpty())
                        <p class="bg-slate-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400 dark:bg-navy-950/60">Mentors</p>
                        <div class="divide-y divide-slate-100 dark:divide-navy-800">
                            @foreach ($mentorConversations as $conversation)
                                @include('alumni.messages.partials.conversation-item')
                            @endforeach
                        </div>
                    @endif

                    @if ($otherConversations->isNotEmpty())
                        <p class="bg-slate-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400 dark:bg-navy-950/60">Messages</p>
                        <div class="divide-y divide-slate-100 dark:divide-navy-800">
                            @foreach ($otherConversations as $conversation)
                                @include('alumni.messages.partials.conversation-item')
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Thread panel --}}
        <div class="flex flex-1 flex-col bg-slate-50 dark:bg-navy-950 {{ $activeConversation ? 'flex' : 'hidden sm:flex' }}" id="thread-panel">
            @include('alumni.messages.partials.thread')
        </div>
    </div>

    @push('scripts')
    <script>
        window.AlumniMessages = {
            currentId: {{ $activeConversation?->id ?? 'null' }},

            openConversation(id, btn) {
                document.querySelectorAll('#conversation-list button').forEach((el) => el.classList.remove('bg-navy-50', 'dark:bg-navy-800'));
                if (btn) btn.classList.add('bg-navy-50', 'dark:bg-navy-800');

                fetch(`{{ url('/messages') }}/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then((r) => r.text())
                    .then((html) => {
                        document.getElementById('thread-panel').innerHTML = html;
                        document.getElementById('thread-panel').classList.remove('hidden');
                        document.getElementById('thread-panel').classList.add('flex');
                        window.history.replaceState({}, '', `{{ url('/messages') }}/${id}`);
                        this.currentId = id;
                        this.scrollToBottom();
                    });
            },

            scrollToBottom() {
                const box = document.getElementById('message-scroll');
                if (box) box.scrollTop = box.scrollHeight;
            },

            sendMessage(event, conversationId) {
                event.preventDefault();
                const form = event.target;
                const input = form.querySelector('[name=body]');
                const body = input.value.trim();
                if (!body) return;

                fetch(`{{ url('/messages') }}/${conversationId}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                    body: new FormData(form),
                })
                    .then((r) => r.json())
                    .then((data) => {
                        document.getElementById('messages-container').insertAdjacentHTML('beforeend', data.html);
                        input.value = '';
                        this.scrollToBottom();
                    });
            },
        };
    </script>
    @endpush
</x-layouts::alumni>
