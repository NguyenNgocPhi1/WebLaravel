<?php 
    $name = $product->languages->first()->pivot->name;
    $canonical = write_url($product->languages->first()->pivot->canonical);
    $image = image($product->image);
    $price = getPrice($product, true);
    // $catName = array_map(function($category, $product){
    //     return ($category['id'] === $product->product_catalogue_id) ? $category['languages'][0]['pivot']['name'] : '';
    // }, $product->product_catalogues->toArray(), [$product])[0];
    $catName = $product->product_catalogues->first()->languages->first()->pivot->name;

    $review = getReview($product);
?>
<div class="product-item product">
    @if($price['percent'] > 0)
        <div class="badge badge-bg<?php echo rand(1,3) ?>">-{{ $price['percent'] }}%</div>
    @endif
    <a href="{{ $canonical }}" class="image img-scaledown img-zoomin"><img src="{{ $image }}" alt="{{ $name }}"></a>
    <div class="info">
        <div style="margin-bottom: 0" class="category-title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $catName }}</a></div>
        <h3 style="margin: 0" class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h3>
        <div class="rating">
            <div class="uk-flex uk-flex-middle">
                <div class="star">
                    @for($j = 1; $j <= $review['star']; $j++)
                        <i class="fa fa-star"></i>
                    @endfor
                </div>
                <span class="rate-number">({{ $review['count'] }})</span>
            </div>
        </div>
        <div class="product-group">
            <div class="uk-flex uk-flex-middle uk-flex-space-between mt5">
                {!! $price['html'] !!}
                <div class="addcart">
                    {!! renderQuickBuy($product, $name, $canonical) !!}
                </div>
            </div>
        </div>

    </div>
    <div class="tools">
        <a href="{{ $canonical }}" title="{{ $name }}"><i class="bi bi-lightning-fill"></i></a>
        <a href="{{ $canonical }}" title="{{ $name }}"><i class="bi bi-heart-half"></i></a>
        <a href="{{ $canonical }}" title="{{ $name }}"><i class="fi-rs-shuffle"></i></a>
        <a href="#popup" data-uk-modal title="{{ $name }}"><i class="fi-rs-eye"></i></a>
    </div>
</div>