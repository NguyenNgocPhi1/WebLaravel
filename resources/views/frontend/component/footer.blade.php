<footer class="footer">
    <div class="uk-container uk-container-center">
        <div class="footer-upper">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large-1-5">
                    <div class="footer-contact">
                        <a href="" class="image img-scaledown"><img src="https://themepanthers.com/wp/nest/d1/wp-content/uploads/2022/02/logo.png" alt=""></a>
                        <div class="footer-slogan">Awesome grocery store website template</div>
                        <div class="company-address">
                            <div class="address">Số 16 Ngõ 198 Lê Trọng Tấn, Khương Mai, Thanh Xuân, Hà Nội</div>
                            <div class="phone">Hotline: 0988.778.688</div>
                            <div class="email">Email: info@nestmart.com</div>
                            <div class="hour">Giờ làm việc: 10:00 - 18:00, Mon - Sat</div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-large-3-5">
                    @if(isset($menu['footer-menu']) && count($menu['footer-menu']))
                    <div class="footer-menu">
                        <div class="uk-grid uk-grid-medium">
                            @foreach($menu['footer-menu'] as $key => $val)
                            @php
                                $name = $val['item']->languages->first()->pivot->name;
                                $canonical = write_url($val['item']->languages->first()->pivot->canonical, true, true);
                            @endphp
                            <div class="uk-width-large-1-4">
                                <div class="ft-menu">
                                    <div class="heading">{{ $name }}</div>
                                    @if(isset($val['children']) && count($val['children']))
                                    <ul class="uk-list uk-clearfix">
                                        @foreach($val['children'] as $children)
                                        @php
                                            $childName = $children['item']->languages->first()->pivot->name;
                                            $childCanonical = write_url($children['item']->languages->first()->pivot->canonical, true, true);
                                        @endphp
                                        <li><a href="{{ $childCanonical }}">{{ $childName }}</a></li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                <div class="uk-width-large-1-5">
                    <div class="fanpage-facebook">
                        <div class="ft-menu">
                            <div class="heading">Fanpage Facebook</div>
                            <div class="fanpage">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright">
        <div class="uk-container uk-container-center">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <div class="copyright-text"{!! $system['homepage_copyright'] !!}></div>
                <div class="copyright-contact">
                    <div class="uk-flex uk-flex-middle">
                        <div class="phone-item">
                            <div class="p">Hotline: {{ $system['contact_hotline'] }}</div>
                            <div class="worktime">Làm việc: 8:00 - 22:00</div>
                        </div>
                        <div class="phone-item">
                            <div class="p">Support: {{ $system['contact_phone'] }}</div>
                            <div class="worktime">Hỗ trợ 24/7</div>
                        </div>
                    </div>
                </div>
                <div class="social">
                    <div class="uk-flex uk-flex-middle">
                        <div class="span">Follow us:</div>
                        <div class="social-list">
                            @php
                                $social = ['facebook', 'twitter', 'youtube'];
                            @endphp
                            <div class="uk-flex uk-flex-middle">
                                @foreach($social as $key => $val)
                                <a target="_blank" href="{{ $system['social_'.$val] }}" class=""><i class="fa fa-{{ $val }}"></i></a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>