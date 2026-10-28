<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5 select-none']) }}>
    <!-- CRM Icon Card -->
    <div class="relative h-9 w-9 shrink-0 flex items-center justify-center rounded-xl bg-teal-500/20 border border-teal-500/30 shadow-[0_0_15px_rgba(20,184,166,0.15)] overflow-hidden">
        <div class="absolute inset-0 bg-teal-400/10 blur-[2px]"></div>
        
        <!-- User & Growth SVG Icon -->
        <svg class="w-5 h-5 text-teal-400 drop-shadow-[0_0_8px_rgba(45,212,191,0.6)] relative z-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
            <circle cx="8.5" cy="7" r="4" />
            <polyline points="17 11 19 13 23 9" />
        </svg>
    </div>

    <!-- Typography -->
    <span class="text-xl font-extrabold text-white tracking-tight">
        Simple<span class="text-teal-400 font-bold drop-shadow-[0_0_8px_rgba(45,212,191,0.4)]">CRM</span>
    </span>
</div>