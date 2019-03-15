$(function(){
    // フッター固定
    var $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
        $ftr.attr({'style':'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'});
    }

    // いいね///////
    var $good;
    var goodPicId;

    $good=$('.js_click_good') || null;
    goodPicId=$good.data('picid') || null;
    if(goodPicId !==undefined && goodPicId !== null){
        $good.on('click',function(){
            $this=$(this);
            $.ajax({
                type:"POST",
                url:"favo.php",
                data:{p_id:goodPicId}
            }).done(function(data){
                console.log('ajax ok');
                console.log(goodPicId);
                $this.toggleClass('active');
            }).fail(function(msg){
                console.log('ajax error');
            });
        });
    }

    // 画像ライブプレビュー
    var $icon_form=$('.icon_form');
    var $input_img=$('.input_img');

    $icon_form.on('dragover',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','3px #ccc dashed');
    });
    $icon_form.on('dragleave',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','none');
    });
    $input_img.on('change',function(e){
        $icon_form.css('border','none');
        var file=this.files[0];
        fileReader=new FileReader();
        var $img_space=$(this).siblings('img');

        fileReader.onload=function(event){
        $img_space.attr('src',event.target.result).css('display','block');
        }
        fileReader.readAsDataURL(file);
    });

    
    $('.alart').slideDown('slow');
    setTimeout(function(){ $('.alart').slideToggle('slow'); }, 5000);
    
});