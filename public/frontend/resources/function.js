(function($) {
	"use strict";
	var HT = {}; // Khai báo là 1 đối tượng
	var timer;
	var $carousel = $(".owl-slide");

	HT.swiperOption = (setting) => {
		let option = {}

		if(setting.animation.length){
			option.effect = setting.animation;
		}	
		if(setting.arrow === 'accept'){
			option.navigation = {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			}
		}
		if(setting.autoplay === 'accept'){
			option.autoplay = {
			    delay: 2000,
			    disableOnInteraction: false,
			}
		}
		if(setting.navigate === 'dots'){
			option.pagination = {
				el: '.swiper-pagination',
			}
		}
		return option
	}
	
	/* MAIN VARIABLE */
	HT.swiper = () => {
		if($('.panel-slide').length){
			let setting = JSON.parse($('.panel-slide').attr('data-setting'))
			let option = HT.swiperOption(setting)
			var swiper = new Swiper(".panel-slide .swiper-container", option);
		}
		
	}

	HT.carousel = () => {
		$carousel.each(function(){
			let _this = $(this);
			let option = _this.find('.owl-carousel').attr('data-owl');
			let owlInit = atob(option);
			owlInit = JSON.parse(owlInit);
			_this.find('.owl-carousel').owlCarousel(owlInit);
		});
		
	} 

	HT.bestSeller = () => {
		var swiper = new Swiper(".panel-besterseller .swiper-container", {
			loop: false,
			pagination: {
				el: '.swiper-pagination',
			},
			spaceBetween: 20,
			slidesPerView: 1,
			breakpoints: {
				415: {
					slidesPerView: 1,
				},
				500: {
				  slidesPerView: 2,
				},
				768: {
				  slidesPerView: 3,
				},
				1280: {
					slidesPerView: 5,
				}
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			
		});
		
	}

	HT.swiperCategory = () => {
		var swiper = new Swiper(".panel-category .swiper-container", {
			loop: false,
			pagination: {
				el: '.swiper-pagination',
			},
			spaceBetween: 20,
			slidesPerView: 3,
			breakpoints: {
				415: {
					slidesPerView: 3,
				},
				500: {
				  slidesPerView: 3,
				},
				768: {
				  slidesPerView: 6,
				},
				1280: {
					slidesPerView: 10,
				}
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			
		});
	}

	HT.swiperBestSeller = () => {
		var swiper = new Swiper(".panel-bestseller .swiper-container", {
			loop: false,
			pagination: {
				el: '.swiper-pagination',
			},
			spaceBetween: 20,
			slidesPerView: 2,
			breakpoints: {
				415: {
					slidesPerView: 1,
				},
				500: {
				  slidesPerView: 2,
				},
				768: {
				  slidesPerView: 3,
				},
				1280: {
					slidesPerView: 4,
				}
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			
		});
	}

	HT.swiperProject = () => {
		var swiper = new Swiper(".panel-project .swiper-container", {
			loop: false,
			pagination: {
				el: '.swiper-pagination',
			},
			spaceBetween: 20,
			slidesPerView: 2,
			breakpoints: {
				415: {
					slidesPerView: 1,
				},
				500: {
				  slidesPerView: 2,
				},
				768: {
				  slidesPerView: 2,
				},
				1280: {
					slidesPerView: 3,
				}
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			
		});
	}

	HT.swiperVideo = () => {
		var swiper = new Swiper(".panel-video .swiper-container", {
			loop: false,
			pagination: {
				el: '.swiper-pagination',
			},
			spaceBetween: 20,
			slidesPerView: 2,
			breakpoints: {
				415: {
					slidesPerView: 1,
				},
				500: {
				  slidesPerView: 2,
				},
				768: {
				  slidesPerView: 2,
				},
				1280: {
					slidesPerView: 4,
				}
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			
		});
	}
	
	
	

	HT.wow = () => {
		var wow = new WOW(
			{
			  boxClass:     'wow',      // animated element css class (default is wow)
			  animateClass: 'animated', // animation css class (default is animated)
			  offset:       0,          // distance to the element when triggering the animation (default is 0)
			  mobile:       true,       // trigger animations on mobile devices (default is true)
			  live:         true,       // act on asynchronously loaded content (default is true)
			  callback:     function(box) {
				// the callback is fired every time an animation is started
				// the argument that is passed in is the DOM node being animated
			  },
			  scrollContainer: null,    // optional scroll container selector, otherwise use window,
			  resetAnimation: true,     // reset animation on end (default is true)
			}
		  );
		  wow.init();


	}// arrow function

	HT.niceSelect = () => {
		if($('.nice-select').length){
			$('.nice-select').niceSelect();
		}
		
	}

	HT.openPreviewVideo = () => {
		$('.choose-video').on('click', function(e){
			e.preventDefault()
			let _this = $(this)
			let option = {
				id: _this.attr('data-id')
			}
			$.ajax({
				url: 'ajax/post/video', 
				type: 'GET', 
				data: option, 
				dataType: 'json', 
				beforeSend: function() {
					
				},
				success: function(res) {
					$('.video-preview .video-play').html(res.html)
					$('.video-preview video').attr('autoplay', 'autoplay');
					$('.video-preview iframe').attr('src', function (i, val) {
						return val + (val.indexOf('?') !== -1 ? '&' : '?') + 'autoplay=1';
					});
				},
			});

		})
	}
	
	HT.popupSwiperSlide = () => {
		document.querySelectorAll(".popup-gallery").forEach(popup => {
			var swiper = new Swiper(popup.querySelector(".swiper-container"), {
				loop: true,
				autoplay: {
					delay: 2000,
					disableOnInteraction: false,
				},
				pagination: {
					el: '.swiper-pagination',
				},
				thumbs: {
					swiper: {
						el: popup.querySelector('.swiper-container-thumbs'),
						slidesPerView: 4,
						spaceBetween: 10,
						slideToClickedSlide: true,
					},
				}			
			});
		})
		
	}

	HT.selectVariantProduct = () => {
		if($('.choose-attribute').length){
			$(document).on('click', '.choose-attribute', function(e){
				e.preventDefault()
				let _this = $(this)
				let attribute_id = _this.attr('data-attributeid')
				let attribute_name = _this.text()
				_this.parents('.attribute-item').find('span').html(attribute_name)
				_this.parents('.attribute-value').find('.choose-attribute').removeClass('active')
				_this.addClass('active')
				HT.handleAttribute()
			})
		}
	}

	HT.handleAttribute = () => {
		let attribute_id = []
		let flag = true
		$('.attribute-value .choose-attribute').each(function(){
			let _this = $(this)
			if(_this.hasClass('active')){
				attribute_id.push(_this.attr('data-attributeid'))
			}
		})
		$('.attribute').each(function(){
			if($(this).find('.choose-attribute.active').length === 0){
				flag = false
				return false;
			}
		})
		
		if(flag){
			$.ajax({
				url: 'ajax/product/loadVariant', 
				type: 'GET', 
				data: {
					'attribute_id' : attribute_id,
					'product_id' : $('input[name=product_id]').val(),
					'language_id' : $('input[name=language_id]').val(),
				}, 
				dataType: 'json', 
				beforeSend: function() {
					
				},
				success: function(res) {
					HT.setupVariantPrice(res)
					HT.setupVariantGallery(res)
					HT.setupVariantName(res)
					HT.setupVariantUrl(res, attribute_id)

				},
			});
		}
		
	}

	HT.setupVariantUrl = (res, attribute_id) => {
		let queryString = '?attribute_id=' + attribute_id.join(',')
		let productCanonical = $('.productCanonical').val()
		productCanonical = productCanonical + queryString
		let stateObject = { attribute_id: attribute_id}
		history.pushState(stateObject, "Page Title", productCanonical)
	}

	HT.setupVariantPrice = (res) => {
		$('.popup-product .price').html(res.variantPrice.html)
	}

	HT.setupVariantName  = (res) => {
		let productName = $('.productName').val()
		let productVariantName = productName + ' ' + res.variant.languages[0].pivot.name
		$('.product-main-title span').html(productVariantName)
	}

	HT.setupVariantGallery = (gallery) => {
		let album = gallery.variant.album.split(',')
		let html = `
			<div class="swiper-container">
				<div class="swiper-wrapper big-pic">
		`
		album.forEach((val) => {
			html += `
				<div class="swiper-slide" data-swiper-autoplay="2000">
					<a href="${val}" class="image img-cover"><img src="${val}" alt="${val}"></a>
				</div>
			`
		})
		html += `
			</div>
				<div class="swiper-pagination"></div>
			</div>
			<div class="swiper-container-thumbs">
				<div class="swiper-wrapper pic-list">
		`
		album.forEach((val) => {
			html += `
				<div class="swiper-slide">
					<span  class="image img-cover"><img src="${val}" alt="${val}"></span>
				</div>
			`
		})
		html += `
				</div>
			</div>
		`
		if(album.length){
			$('.popup-gallery').html(html)
			HT.popupSwiperSlide()
		}
		
	}

	HT.loadProductVariant = () => {
		let attributeCatalogue = JSON.parse($('.attributeCatalogue').val())
		if(typeof attributeCatalogue != 'undefined' && attributeCatalogue.length){
			HT.handleAttribute()
		}
	}

	$(document).ready(function(){
		HT.wow()
		HT.swiperCategory()
		HT.swiperBestSeller()
		// HT.swiperIntro()
		HT.bestSeller()
		HT.swiperProject()
		HT.swiperVideo()
		
		/* CORE JS */
		HT.swiper()
		HT.niceSelect()		
		HT.carousel()


		HT.openPreviewVideo()
		HT.popupSwiperSlide()
		HT.selectVariantProduct()
		HT.loadProductVariant()
	});

})(jQuery);



addCommas = (nStr) => { 
    nStr = String(nStr);
    nStr = nStr.replace(/\./gi, "");
    let str ='';
    for (let i = nStr.length; i > 0; i -= 3){
        let a = ( (i-3) < 0 ) ? 0 : (i-3);
        str= nStr.slice(a,i) + '.' + str;
    }
    str= str.slice(0,str.length-1);
    return str;
}