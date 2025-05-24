<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataPagination extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $paginator,
        public array $perPageOptions = [10, 50, 100, 200],
        public string $perPageProperty = 'perPage',
        public string $pageName = 'page'
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.data-pagination');
    }
}
