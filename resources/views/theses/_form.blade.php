@csrf

<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-input-label for="title" :value="__('Thesis title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $thesis->title ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div>
        <x-input-label for="program" :value="__('Program / department')" />
        <x-text-input id="program" name="program" type="text" class="mt-1 block w-full" :value="old('program', $thesis->program ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('program')" />
    </div>

    <div>
        <x-input-label for="academic_year" :value="__('Academic year')" />
        <x-text-input id="academic_year" name="academic_year" type="text" class="mt-1 block w-full" placeholder="2025-2026" :value="old('academic_year', $thesis->academic_year ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('academic_year')" />
    </div>

    <div>
        <x-input-label for="department" :value="__('Department')" />
        <x-text-input id="department" name="department" type="text" class="mt-1 block w-full" :value="old('department', $thesis->department ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('department')" />
    </div>

    <div>
        <x-input-label for="school_year" :value="__('School year')" />
        <x-text-input id="school_year" name="school_year" type="text" class="mt-1 block w-full" placeholder="2025-2026" :value="old('school_year', $thesis->school_year ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('school_year')" />
    </div>

    <div>
        <x-input-label for="adviser_id" :value="__('Adviser')" />
        <select id="adviser_id" name="adviser_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
            <option value="">{{ __('Select an adviser') }}</option>
            @foreach ($advisers as $adviser)
                <option value="{{ $adviser->id }}" @selected(old('adviser_id', $thesis->adviser_id ?? '') == $adviser->id)>{{ $adviser->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('adviser_id')" />
    </div>

    @if (isset($thesis) && auth()->user()->role !== \App\Models\User::STUDENT)
        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                @foreach (\App\Models\Thesis::statuses() as $status)
                    <option value="{{ $status }}" @selected(old('status', $thesis->status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('status')" />
        </div>
    @endif

    <div class="md:col-span-2">
        <x-input-label for="keywords" :value="__('Keywords')" />
        <x-text-input id="keywords" name="keywords" type="text" class="mt-1 block w-full" placeholder="Separate keywords with commas" :value="old('keywords', $thesis->keywords ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('keywords')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="abstract" :value="__('Abstract')" />
        <textarea id="abstract" name="abstract" rows="6" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('abstract', $thesis->abstract ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('abstract')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="file" :value="__('Thesis PDF')" />
        <input id="file" name="file" type="file" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300" />
        <p class="mt-1 text-sm text-gray-500">{{ __('PDF only, up to 10 MB.') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('file')" />
    </div>
</div>

<div class="mt-6 flex items-center gap-4">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('theses.index') }}" class="text-sm text-gray-600 underline dark:text-gray-400">{{ __('Cancel') }}</a>
</div>