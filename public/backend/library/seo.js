(function($){
    "use strict"
    var HT = {}

    HT.seoPreview = () => {
        $('input[name=meta_title]').on('keyup', function(){
            let input = $(this)
            let value = input.val()
            $('.meta-title').html(value)
        })
        $('.seo-canonical').each(function(){
            let _this = $(this)
            _this.css({
                'padding-left': $('.baseUrl').outerWidth() + 10
            })
        })
        $('input[name=canonical]').on('keyup', function(){
            let input = $(this)
            let value = HT.removeUtf8(input.val())
            $('.canonical').html(BASE_URL + value + SUFFIX)
        })
        $('textarea[name=meta_description]').on('keyup', function(){
            let input = $(this)
            let value = input.val()
            $('.meta-description').html(value)
        })
        $(document).on('keyup', '.change-title', function(e){
            let _this = $(this)
            let flag = _this.attr('data-flag')
            let canonical = HT.removeUtf8(_this.val())
            if(flag == 0){
                $('.seo-canonical').val(canonical).trigger('keyup')
            }
        })
    }

    HT.removeUtf8 = (str) =>{
        str = str.toLowerCase()
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a")
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e")
        str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i")
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o")
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u")
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y")
        str = str.replace(/đ/g, "d")
        str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A")
        str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E")
        str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I")
        str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O")
        str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U")
        str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y")
        str = str.replace(/Đ/g, "D")
        str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|\$|_|`|{|}|\||\\/g," ")
        str = str.replace(/ /g,"-")
        str = str.replace(/^\-+|\-+$/g,"")
      return str
    }
    
    $(document).ready(function(){
        HT.seoPreview()
    })
})(jQuery)