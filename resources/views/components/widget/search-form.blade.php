<form class="needs-validation" novalidate="" method="GET" action="{{ route('search.content') }}">
    <div class="input-group overflow-hidden border-0 border-bottom border-dark border-2 px-0 bg-transparent align-items-end">
        <input name="search" type="text" class="form-control bg-transparent border-0 outline-0 ps-0 pe-2 shadow-none ms-0 pb-2" placeholder="{{ __('Search...') }}" aria-label="{{ __('Search...') }}" required>
        <button type="submit" class="btn bg-transparent p-0 mb-1">
            <i class="bi bi-arrow-up-right-circle-fill fs-2 lh-1"></i>
        </button>
    </div>
    <input type="hidden" name="language" value="{{ app()->getLocale() }}">
</form>