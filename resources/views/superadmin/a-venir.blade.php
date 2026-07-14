@extends('layouts.superadmin')

@section('title', $titre)

@section('content')
<div class="max-w-[720px] mx-auto">
    <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Super Admin</div>
    <h1 class="font-display font-medium text-[30px] text-ink mt-1 mb-6">{{ $titre }}</h1>

    <div class="bg-white border border-line rounded-xl p-8 text-center">
        <div class="w-14 h-14 rounded-full bg-paper-dim flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-teal" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/></svg>
        </div>
        <h2 class="font-display font-medium text-[19px] text-ink mb-1.5">Section à venir</h2>
        <p class="text-[14px] text-muted leading-relaxed max-w-[420px] mx-auto">
            Le module « {{ $titre }} » sera disponible prochainement. En attendant, retrouve l'essentiel sur le
            <a href="{{ route('superadmin.dashboard') }}" class="text-teal font-semibold border-b border-gold pb-px">tableau de bord</a>.
        </p>
    </div>
</div>
@endsection
