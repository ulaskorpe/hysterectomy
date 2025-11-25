<a href="{{$category->uri->final_uri}}" title="{{$category->name}}" class="vstack gap-2 align-items-center text-center text-decoration-none link-dark has-border-link">

    @if ($category->main_image->count() > 0)
    <img loading="lazy" src="{{$category->main_image[0]->original_url}}" class="img-fluid mb-3" width="{{$category->main_image[0]->custom_properties['width']}}" height="{{$category->main_image[0]->custom_properties['height']}}" alt="{{$category->name}}">
    @else
    <div class="box-100 rounded-circle border border-secondary border-2 d-flex align-items-center justify-content-center">
        <i class="bi bi-image fs-1 text-primary"></i>
    </div>
    @endif

    <h3 class="text-for-border p-responsive">{{$category->name}}</h3>

</a>