@if (isset($breadcrumbs) && count($breadcrumbs))
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['name'] }}</li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>

    <style>
        .breadcrumb {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: '>';
            color: #6c757d;
        }
        .breadcrumb-button {
            border: none;
            background-color: transparent;
            color: #007bff;
            text-decoration: none;
            font-size: 1rem;
            padding: 0;
        }
        .breadcrumb-button:hover, .breadcrumb-button:focus {
            color: #0056b3;
            text-decoration: underline;
        }
        .breadcrumb-item.active .breadcrumb-button {
            color: #495057;
            font-weight: bold;
            cursor: default;
            text-decoration: none;
        }
        @media (max-width: 576px) {
            .breadcrumb {
                padding: 0.5rem;
                font-size: 0.875rem;
            }
            .breadcrumb-item {
                display: block;
                margin-bottom: 0.5rem;
            }
            /* .breadcrumb-item + .breadcrumb-item::before {
                content: none;
            } */
        }
    </style>
@endif