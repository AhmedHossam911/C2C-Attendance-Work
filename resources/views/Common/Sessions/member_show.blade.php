@extends('Common.Layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        {{-- Back Link --}}
        <div class="mb-6">
            <a href="{{ route('sessions.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors">
                <i class="bi bi-arrow-left"></i> Back to Sessions
            </a>
        </div>

        {{-- Logic --}}
        @php
            $myRecord = $records->where('user_id', Auth::id())->first();
            $userFeedback = \App\Models\SessionFeedback::where('attendance_session_id', $session->id)
                ->where('user_id', Auth::id())
                ->first();

            $hasAttended = $myRecord && in_array($myRecord->status, ['present', 'late']);
            $isSessionClosed = $session->status === 'closed';

            // Logic for feedback eligibility
            $isEligibleForFeedback = $hasAttended && $isSessionClosed;
            $readOnly = $userFeedback ? true : false;
        @endphp

        {{-- Attendance Status Card --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl p-6 mb-10 border border-slate-200 dark:border-slate-700 shadow-sm text-center">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-6">Attendance Status</h3>

            @if ($myRecord)
                <div class="flex flex-col items-center">
                    <div
                        class="h-16 w-16 rounded-full {{ $myRecord->status === 'present' ? 'bg-green-100 text-green-600' : ($myRecord->status === 'late' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600') }} flex items-center justify-center text-3xl mb-3 shadow-sm">
                        <i
                            class="bi bi-{{ $myRecord->status === 'present' ? 'check-lg' : ($myRecord->status === 'late' ? 'exclamation-lg' : 'x-lg') }}"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">{{ ucfirst($myRecord->status) }}</h4>
                </div>
            @else
                <div class="flex flex-col items-center py-2">
                    @if ($isSessionClosed)
                        <div
                            class="h-16 w-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-3xl mb-3 shadow-sm">
                            <i class="bi bi-x-lg"></i>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Absent</h4>
                        <p class="text-sm text-slate-500">You did not attend this session.</p>
                    @else
                        <div
                            class="h-16 w-16 rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500 flex items-center justify-center text-3xl mb-3">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>
                        <h4 class="text-lg font-bold text-slate-700 dark:text-slate-300 mb-1">Not Scanned Yet</h4>
                        <p class="text-xs text-slate-500">Scan the QR code to mark attendance.</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Feedback Section --}}
        @if (!$isEligibleForFeedback && !$userFeedback)
            {{-- Ineligible State --}}
            <div
                class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-8 border border-dashed border-slate-300 dark:border-slate-700 text-center">
                <div
                    class="h-12 w-12 bg-slate-200 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                    <i class="bi bi-lock-fill text-xl"></i>
                </div>
                <h4 class="font-bold text-slate-800 dark:text-white mb-2">Feedback Unavailable</h4>
                <p class="text-sm text-slate-500 max-w-xs mx-auto">
                    @if (!$isSessionClosed)
                        Session is still open. Feedback will be available once the session ends.
                    @elseif (!$hasAttended)
                        You were marked as Absent. Only attendees can submit feedback.
                    @endif
                </p>
            </div>
        @else
            {{-- Header --}}
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">
                    {{ $readOnly ? 'My Feedback' : 'Session Feedback' }}
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mb-6">
                    <span class="font-medium text-slate-900 dark:text-white">{{ $session->title }}</span>
                </p>

                <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-full text-xs font-semibold text-slate-600 dark:text-slate-400">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>{{ $readOnly ? 'Submitted Anonymously' : 'Anonymous Submission' }}</span>
                </div>
            </div>

            @if (session('success'))
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div
                        class="w-16 h-16 bg-green-100 dark:bg-green-500/10 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-xl mb-2">Feedback Received</h4>
                    <p class="text-slate-500 dark:text-slate-400 mb-8">Thank you for helping us improve.</p>
                    <a href="{{ route('sessions.index') }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-xl hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors">
                        Back to Sessions
                    </a>
                </div>
            @else
                {{-- Read Only Banner --}}
                @if ($readOnly)
                    <div
                        class="mb-12 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-2xl p-6 text-center">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">
                            <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white mb-1">Feedback Submitted</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">This is a read-only copy of your response.</p>
                    </div>
                @endif

                <form action="{{ route('sessions.feedback', $session) }}" method="POST" class="flex flex-col gap-16">
                    @csrf
                    @if ($readOnly)
                        <fieldset disabled class="contents">
                    @endif

                    {{-- Section 1: Instructor & Content --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl p-6 md:p-8 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden">
                        <h3
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-8 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                            Part I: Experience</h3>

                        <div class="space-y-10">
                            {{-- Objectives --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-900 dark:text-white mb-4">
                                    Did the instructor clearly state and achieve the learning objectives? <span
                                        class="text-red-500">*</span>
                                </label>
                                <div
                                    class="flex flex-row-reverse justify-end gap-2 group/stars {{ $readOnly ? 'pointer-events-none' : '' }}">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="obj_star_{{ $i }}" name="objectives_clarity"
                                            value="{{ $i }}" class="peer sr-only" required
                                            {{ $userFeedback && $userFeedback->objectives_clarity == $i ? 'checked' : '' }}>
                                        <label for="obj_star_{{ $i }}"
                                            class="text-3xl transition-colors cursor-pointer
                                            {{ $readOnly
                                                ? ($userFeedback && $userFeedback->objectives_clarity >= $i
                                                    ? 'text-amber-400'
                                                    : 'text-slate-200 dark:text-slate-700')
                                                : 'text-slate-200 dark:text-slate-700 peer-checked:text-amber-400 hover:text-amber-400 peer-hover:text-amber-400' }}"
                                            title="{{ $i }} Stars">
                                            <i class="bi bi-star-fill"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            {{-- Instructor Understanding --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-900 dark:text-white mb-4">
                                    How effectively did the instructor communicate and explain the content? <span
                                        class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-5 gap-3">
                                    @foreach (range(1, 5) as $i)
                                        @php
                                            $isSelected =
                                                $userFeedback && $userFeedback->instructor_understanding == $i;
                                            $isMuted = $readOnly && !$isSelected;
                                        @endphp
                                        <label
                                            class="cursor-pointer group text-center {{ $isMuted ? 'opacity-40 grayscale scale-95' : '' }}">
                                            <input type="radio" name="instructor_understanding"
                                                value="{{ $i }}" class="peer sr-only" required
                                                {{ $isSelected ? 'checked' : '' }}>

                                            <div
                                                class="h-12 w-full rounded-xl border flex items-center justify-center font-bold transition-all
                                                {{ $readOnly
                                                    ? ($isSelected
                                                        ? 'bg-slate-900 text-white dark:bg-brand-blue border-transparent shadow-md scale-105'
                                                        : 'bg-slate-50 dark:bg-slate-700/30 text-slate-400 border-slate-200 dark:border-slate-700')
                                                    : 'bg-slate-50 dark:bg-slate-700/50 border-slate-200 dark:border-slate-600 text-slate-400 peer-checked:bg-slate-900 peer-checked:text-white dark:peer-checked:bg-brand-blue dark:peer-checked:border-brand-blue peer-checked:border-transparent' }}">
                                                {{ $i }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <div
                                    class="flex justify-between mt-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    <span>Poorly</span>
                                    <span>Effectively</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Logistics --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl p-6 md:p-8 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden">
                        <h3
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-10 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                            Part II: Logistics</h3>

                        <div class="space-y-12">
                            {{-- Overall Satisfaction --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-900 dark:text-white mb-4">
                                    How would you rate your overall satisfaction? <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                                    @foreach (range(1, 10) as $i)
                                        @php
                                            $isSelected = $userFeedback && $userFeedback->overall_satisfaction == $i;
                                            $isMuted = $readOnly && !$isSelected;
                                        @endphp
                                        <label class="cursor-pointer {{ $isMuted ? 'opacity-30 scale-90' : '' }}">
                                            <input type="radio" name="overall_satisfaction" value="{{ $i }}"
                                                class="peer sr-only" required {{ $isSelected ? 'checked' : '' }}>
                                            <div
                                                class="aspect-square flex items-center justify-center rounded-xl border text-xs font-bold transition-all
                                                {{ $readOnly
                                                    ? ($isSelected
                                                        ? 'bg-slate-900 text-white dark:bg-brand-blue border-transparent shadow-md scale-110 z-10'
                                                        : 'bg-slate-50 dark:bg-slate-700/30 text-slate-400 border-slate-200 dark:border-slate-700')
                                                    : 'bg-slate-50 dark:bg-slate-700/30 text-slate-500 border-slate-200 dark:border-slate-700 peer-checked:bg-slate-900 peer-checked:text-white dark:peer-checked:bg-brand-blue dark:peer-checked:text-white peer-checked:border-transparent hover:border-slate-400 dark:hover:border-slate-500' }}">
                                                {{ $i }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <div
                                    class="flex justify-between mt-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    <span>Low</span>
                                    <span>High</span>
                                </div>
                            </div>

                            {{-- Room Suitability --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-900 dark:text-white mb-4">
                                    Was the venue suitable for the session? <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                                    @php
                                        $options = [
                                            [
                                                'label' => 'Definitely Yes',
                                                'val' => 'Definitely Yes',
                                                'icon' => 'bi-emoji-smile-fill',
                                            ],
                                            [
                                                'label' => 'Probably Yes',
                                                'val' => 'Probably Yes',
                                                'icon' => 'bi-emoji-smile',
                                            ],
                                            ['label' => 'Neutral', 'val' => 'Neutral', 'icon' => 'bi-emoji-neutral'],
                                            [
                                                'label' => 'Probably No',
                                                'val' => 'Probably No',
                                                'icon' => 'bi-emoji-frown',
                                            ],
                                            [
                                                'label' => 'Definitely No',
                                                'val' => 'Definitely No',
                                                'icon' => 'bi-emoji-frown-fill',
                                            ],
                                        ];
                                    @endphp
                                    @foreach ($options as $opt)
                                        @php
                                            $isSelected =
                                                $userFeedback && $userFeedback->room_suitability == $opt['val'];
                                            $isMuted = $readOnly && !$isSelected;
                                        @endphp
                                        <label class="cursor-pointer group {{ $isMuted ? 'opacity-40 grayscale' : '' }}">
                                            <input type="radio" name="room_suitability" value="{{ $opt['val'] }}"
                                                class="peer sr-only" required {{ $isSelected ? 'checked' : '' }}>
                                            <div
                                                class="h-full px-3 py-4 rounded-xl border text-center flex flex-col items-center gap-2 transition-all
                                                {{ $readOnly
                                                    ? ($isSelected
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-brand-blue dark:text-blue-400 border-brand-blue dark:border-blue-500 shadow-md transform scale-[1.02]'
                                                        : 'bg-slate-50 dark:bg-slate-700/30 text-slate-400 border-slate-200 dark:border-slate-700')
                                                    : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700 peer-checked:bg-slate-900 peer-checked:text-white dark:peer-checked:bg-white dark:peer-checked:text-slate-900 peer-checked:border-transparent hover:border-slate-300' }}">
                                                <i class="bi {{ $opt['icon'] }} text-xl"></i>
                                                <span
                                                    class="text-[10px] font-bold uppercase tracking-wide leading-tight">{{ $opt['label'] }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: C2C System --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl p-6 md:p-8 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden">
                        <h3
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-10 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                            Part III: System</h3>

                        <div class="space-y-12">
                            {{-- System Rating --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-900 dark:text-white mb-4">
                                    Rate experience with C2C Management System <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                                    @foreach (range(1, 10) as $i)
                                        @php
                                            $isSelected =
                                                $userFeedback && $userFeedback->attendance_system_rating == $i;
                                            $isMuted = $readOnly && !$isSelected;
                                        @endphp
                                        <label class="cursor-pointer {{ $isMuted ? 'opacity-30 scale-90' : '' }}">
                                            <input type="radio" name="attendance_system_rating"
                                                value="{{ $i }}" class="peer sr-only" required
                                                {{ $isSelected ? 'checked' : '' }}>
                                            <div
                                                class="aspect-square flex items-center justify-center rounded-xl border text-xs font-bold transition-all
                                                {{ $readOnly
                                                    ? ($isSelected
                                                        ? 'bg-slate-900 text-white dark:bg-brand-blue border-transparent shadow-md scale-110'
                                                        : 'bg-slate-50 dark:bg-slate-700/30 text-slate-400 border-slate-200 dark:border-slate-700')
                                                    : 'bg-slate-50 dark:bg-slate-700/30 text-slate-500 border-slate-200 dark:border-slate-700 peer-checked:bg-slate-900 peer-checked:text-white dark:peer-checked:bg-brand-blue dark:peer-checked:text-white peer-checked:border-transparent hover:border-slate-400 dark:hover:border-slate-500' }}">
                                                {{ $i }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Comments --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                    How can we improve the System?
                                </label>
                                @if ($readOnly)
                                    <div
                                        class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700 text-sm text-slate-700 dark:text-slate-300 min-h-[3rem]">
                                        {{ $userFeedback->attendance_system_suggestions ?: 'No suggestions provided.' }}
                                    </div>
                                @else
                                    <textarea name="attendance_system_suggestions" id="attendance_system_suggestions" rows="2"
                                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:border-slate-900 dark:focus:border-brand-blue focus:ring-0 transition-colors p-3 text-sm focus:shadow-sm"
                                        placeholder="Your ideas for the C2C Management System...">{{ $userFeedback ? $userFeedback->attendance_system_suggestions : '' }}</textarea>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Additional Comments (Merged Section) --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl p-6 md:p-8 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden">
                        <h3
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-10 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                            Part IV: Final Thoughts</h3>

                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                    Session Comments
                                </label>
                                @if ($readOnly)
                                    <div
                                        class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700 text-sm text-slate-700 dark:text-slate-300 min-h-[5rem]">
                                        {{ $userFeedback->feedback ?: 'No comments provided.' }}
                                    </div>
                                @else
                                    <textarea name="feedback" id="feedback" rows="3"
                                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:border-slate-900 dark:focus:border-brand-blue focus:ring-0 transition-colors p-3 text-sm focus:shadow-sm"
                                        placeholder="Any additional feedback on the session...">{{ $userFeedback ? $userFeedback->feedback : '' }}</textarea>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                    Future Topics
                                </label>
                                @if ($readOnly)
                                    <div
                                        class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700 text-sm text-slate-700 dark:text-slate-300 min-h-[5rem]">
                                        {{ $userFeedback->future_suggestions ?: 'No suggestions provided.' }}
                                    </div>
                                @else
                                    <textarea name="future_suggestions" id="future_suggestions" rows="3"
                                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:border-slate-900 dark:focus:border-brand-blue focus:ring-0 transition-colors p-3 text-sm focus:shadow-sm"
                                        placeholder="What would you like to see next?">{{ $userFeedback ? $userFeedback->future_suggestions : '' }}</textarea>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-6 pb-8">
                        @if ($readOnly)
                            </fieldset> <!-- Close fieldset -->
                            <a href="{{ route('sessions.index') }}"
                                class="w-full py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl shadow-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-all text-base flex items-center justify-center gap-2 group">
                                <i class="bi bi-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                                <span>Back to Sessions</span>
                            </a>
                        @else
                            <button type="submit"
                                class="w-full py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all text-base flex items-center justify-center gap-2 group">
                                <span>Submit Feedback</span>
                                <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </button>
                            <p class="text-center text-xs text-slate-400 mt-4">
                                By submitting, you agree to provide honest feedback.
                            </p>
                        @endif
                    </div>
                </form>
            @endif
        @endif
    </div>
@endsection
