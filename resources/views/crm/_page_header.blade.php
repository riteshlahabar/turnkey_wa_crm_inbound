<div class="row mb-3">
    <div class="col-sm-12">
        <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
            <div>
                <h4 class="page-title mb-1">{{ $title ?? '' }}</h4>
                @if(!empty($subtitle))
                    <p class="text-muted mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            <div>{{ $slot ?? '' }}</div>
        </div>
    </div>
</div>
