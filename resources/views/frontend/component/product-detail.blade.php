@php
    $name = $product->name;
    $canonical = write_url($product->canonical);
    $image = image($product->image);
    $price = getPrice($product, true);
    $catName = $productCatalogue->name;
    $review = getReview($product);
    $description = $product->description;
    $attributeCatalogue = $product->attributeCatalogue;
    $gallery = json_decode($product->album);
@endphp
<div class="panel-body">
    <div class="uk-grid uk-grid-medium">
        <div class="uk-width-large-3-4">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large-1-2">
                    <div class="popup-gallery">
                        <div class="swiper-container">
                            <div class="swiper-wrapper big-pic">
                                <?php foreach($gallery as $key => $val){  ?>
                                <div class="swiper-slide" data-swiper-autoplay="2000">
                                    <a href="<?php echo $val ?>" class="image img-cover"><img src="<?php echo $val?>" alt="<?php echo $val ?>"></a>
                                </div>
                                <?php }  ?>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                        <div class="swiper-container-thumbs">
                            <div class="swiper-wrapper pic-list">
                                <?php foreach($gallery as $key => $val){  ?>
                                <div class="swiper-slide">
                                    <span  class="image img-cover"><img src="<?php echo $val?>" alt="<?php echo $val ?>"></span>
                                </div>
                                <?php }  ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-large-1-2">
                    <div class="popup-product">
                        <h1 class="title product-main-title"><span>{{ $name }}</span>
                        </h1>
                        <div class="rating">
                            <div class="uk-flex uk-flex-middle">
                                <div class="author">Đánh giá:</div>
                                <div class="star">
                                    <?php for($i = 0; $i<=4; $i++){ ?>
                                    <i class="fa fa-star"></i>
                                    <?php }  ?>
                                </div>
                                <div class="rate-number">(65 reviews)</div>
                            </div>
                        </div>
                        {!! $price['html'] !!}
                        <div class="description">
                            {!! $description !!}
                        </div>
                        @include('frontend.product.product.component.variant')
                        <div class="quantity">
                            <div class="text">Quantity</div>
                            <div class="uk-flex uk-flex-middle">
                                <div class="quantitybox uk-flex uk-flex-middle">
                                    <div class="plus quantity-button"><i class="bi bi-plus" style="font-size: 24px"></i></div>
                                    <input type="text" name="" value="1" class="quantity-text">
                                    <div class="minus quantity-button"><i class="bi bi-dash" style="font-size: 24px"></i></div>
                                </div>
                                <div class="btn-group uk-flex uk-flex-middle">
                                    <div class="btn-item btn-1 addToCart" data-id="{{ $product->id }}"><a href="" title="">Thêm vào giỏ hàng</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-width-large-1-4">
            <div class="aside">
                @if(!is_null($category))
                @foreach($category as $key => $val)
                @php
                    $name = $val['item']->languages->first()->pivot->name;
                @endphp
                <div class="aside-panel aside-catagory">
                    <div class="aside-heading">{{ $name }}</div>
                    @if(!is_null($val['children']) && count($val['children']))
                    <div class="aside-body">
                        <ul class="uk-list uk-clearfix">
                            @foreach($val['children'] as $item)
                            @php
                                $itemName = $item['item']->languages->first()->pivot->name;
                                $itemImage = $item['item']->image;
                                $itemCanonical = write_url($item['item']->languages->first()->pivot->canonical);
                                $productCount = $item['item']->products_count;
                            @endphp
                            <li class="mb20">
                                <div class="categories-item-1">
                                    <a href="{{ $itemCanonical }}" title="{{ $itemName }}" class="uk-flex uk-flex-middle uk-flex-space-between">
                                        <div class="uk-flex uk-flex-middle">
                                            <img src="{{ $itemImage }}" alt="{{ $itemName }}">
                                            <span class="title">{{ $itemName }}</span>
                                        </div>
                                        <span class="total">{{ $productCount }}</span>
                                    </a>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="productName" value="{{ $product->name }}">
<input type="hidden" class="attributeCatalogue" value="{{ json_encode($attributeCatalogue) }}">
<input type="hidden" class="productCanonical" value="{{ write_url($product->canonical) }}">