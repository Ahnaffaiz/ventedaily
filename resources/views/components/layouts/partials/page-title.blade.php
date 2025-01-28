<!-- Page Title Start -->
<div class="flex items-center justify-between mb-6">
    <h4 class="text-lg font-medium text-slate-900 dark:text-slate-200">{{ $title }}</h4>

    <div class="md:flex hidden items-center gap-2.5 font-semibold">
        <div class="flex items-center gap-2">
            <a href="#" class="text-sm font-medium text-slate-700 dark:text-slate-400">{{ env('APP_NAME') }}</a>
        </div>

        <div class="flex items-center gap-2">
            <i class="text-base ri-arrow-right-s-line text-slate-400 rtl:rotate-180"></i>
            <a href="#" class="text-sm font-medium text-slate-700 dark:text-slate-400"
                aria-current="page">{{ $title }}</a>
        </div>
        @if ($subtitle)
            <div class="flex items-center gap-2">
                <i class="text-base ri-arrow-right-s-line text-slate-400 rtl:rotate-180"></i>
                <a href="#" class="text-sm font-medium text-slate-700 dark:text-slate-400">{{ $subtitle }}</a>
            </div>
        @endif
    </div>
</div>
<!-- Page Title End -->
