@foreach($skus as $sku)
    <div>
        <div style="height:250px;width:250px;">
            <img style="max-height:100%;max-width:100%"
                 src="{{ asset($sku->image) }}"
                 alt="{{ asset($sku->image) }}">
        </div>
        <a href="{{ asset('product/' . $sku->code . '-' . $sku->id) }}">
            <div>{{ $sku->name }} {{ $sku->volume }}</div>
        </a>
        цена снизилась
    </div>
@endforeach
