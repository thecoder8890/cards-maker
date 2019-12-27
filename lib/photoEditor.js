var photoEditorIndexs = 0;
jQuery.fn.photoPreview = function(options)
{
    var $ = jQuery;
    var me = this;
    var that = $(this);
    if(!options) options = {};
    
    var settings = {
        width: 500,
        height: 500
    };
    this.modifyText = function (txt)
    {
        that.find('.photo-preview-text').html('<span>' + txt + '</span>');
    };
    
    this.modifyAlign = function ()
    {
        if(!options.view) that.find('.photo-preview-text').css({'text-align': 'center'});
    };
    
    this.modifyAppend = function (value)
    {
        config.append = value;
        this.modifyWidth(config.textWidth);
        that.find('.photo-preview-text').css({'margin-left': value});
    };
    
    this.modifyFont = function (value)
    {
        that.find('.photo-preview-text').css({'font-family': value});
        return value;
    };
    
    this.modifyColor = function (value)
    {
        that.find('.photo-preview-text').css({
            'color': value,
            '-webkit-text-fill-color': value,
        });
    };
    
    this.modifyBorder = function (color, size)
    {
        if(size > 20) size = 20;
        if(size < 0) size = 0;
        var vSize = size / 2;        
        that.find('.photo-preview-text').css({
            '-webkit-text-stroke-color':  color,
            '-webkit-text-stroke-width':  vSize + 'px',
        });
        
        return size;
    };
    
    this.modifyWidth = function (nm)
    {
        if(nm > config.width - 10) nm = Math.floor(config.width - 10);
        config.textWidth = nm;
        that.find('.photo-preview-text').width(nm - config.append);
        
        return nm;
    };
    
    this.modifyFontSize = function (nm)
    {
        if(nm > 72) nm = 72;
        if(nm < 9) nm = 9;
        that.find('.photo-preview-text').css({'font-size': nm});
        
        return nm;
    };

    this.makePhoto = function(photo, $ratio, callBack)
    {
        if(!photo.text || empty(photo.text)) return false;
        $ratio = $ratio ? $ratio : (config.$ratio || 1);
        
        if(that.find('#canvas_' + config.id).length == 0)
        {
            that.append('<canvas id="canvas_' + config.id + '" width="' + (config.width / $ratio) + '" height="' + (config.height / $ratio) + '" style="display: none"></canvas>');
        }
        
        var canvs = that.find("#canvas_" + config.id);
        canvs.clearCanvas().drawImage({
            source: photo.path,
            x: 0,
            y: 0,
            width: config.width / $ratio,
            height: config.height / $ratio,
            sWidth: config.width / $ratio,
            sHeight: config.height / $ratio,
            fromCenter: false,
            load: function()
            {
                canvs.drawText({
                    name: 'myText',
                    layer: true,
                    fillStyle: photo.textColor,
                    strokeStyle: photo.borderColor,
                    strokeWidth: photo.borderSize || 0,
                    x: (parseInt(photo.left) + parseInt(photo.append || 0)) / $ratio,
                    y: (parseInt(photo.top) / $ratio) + (8 / $ratio),
                    fontSize: (parseInt(photo.fontSize) || 20) / $ratio,
                    fontFamily: photo.font || 'impact',
                    text: photo.text,
                    fromCenter: false,
                    maxWidth: (photo.width - parseInt(photo.append || 0)) / $ratio,
                    align: 'left',
                });
                
                if(callBack) callBack();
            }
        });
    };
    
    this.download = function(photo)
    {
        var fileName = photo.text + ".png";    
        this.makePhoto(photo, null, function()
        {
            if (navigator.msSaveBlob) 
            {
                var blob = document.getElementById("canvas_" + config.id).msToBlob();
                    window.navigator.msSaveBlob(blob, fileName);         
            } 
            else 
            {
                var link = document.createElement('a');
                link.download = fileName;
                link.href = document.getElementById("canvas_" + config.id).toDataURL('image/png').replace("image/png", "image/octet-stream");
                var event = new MouseEvent('click');
                link.dispatchEvent(event);
            }
        });
    };
    this.uploadedImages = {};
    this.upload = function(data, callBack)
    {
        var that = this;
        this.selectedImageName = data.name;
        if(!that.uploadedImages[data.name] || data.email)
        {
            var formData = new FormData();
            data.file = navigator.msSaveBlob ? document.getElementById("canvas_" + config.id).msToBlob() : new Blob([dataURItoBlob(data.file)], {type: "image/png"});
            $.each(data, function(k, v)
            {
                if(k == 'file')
                {
                    var blob = v;
                    formData.append(k, blob);
                }
                else if(!isArray(v) && !isObject(v))
                {
                    formData.append(k, v);
                }
                else
                {
                    $.each(v, function(i, vv)
                    {
                        formData.append(k + '[' + i + ']', vv);
                    });
                };
            });
            
            var request = new XMLHttpRequest();
            request.open("POST", "?do=upload");
            function onLoad(oEvent) 
            {
                var res = isJsonText(oEvent.currentTarget.responseText) ? JSON.parse(oEvent.currentTarget.responseText) : {};
                if(res.status) 
                {
                    that.uploadedImages[data.name] = res;
                    callBack(res);
                }
                else
                {
                    alert('Connection error !');
                    waiting(true);
                }
            }
            request.onload = onLoad;
            request.send(formData);
        }
        else
        {
            callBack(that.uploadedImages[data.name]);
        }
    };
    this.selectedImageName = null;
    this.share = function(photo, callBack)
    {
        waiting();
        var that = this;
        this.makePhoto(photo, null, function()
        {
            var image = document.getElementById("canvas_" + config.id).toDataURL();
            that.upload({
                    file: image,
                    dir: photo.name,
                    name: strReplace(" ", "_", photo.text)+(photo.append || 0).toString()+'.png'
                }, function(res)
                {
                    if(callBack) callBack(res);
                    else
                    {
                        waiting(true);
                        $("#sharing").modal('show');
                    }
                }
            );
        });
        
    };
    
    this.shareNow = function(photo, type)
    {
        var that = this;
        function buildURL()
        {                        
            return $('base:eq(0)').attr('href') + 'index.php?do=share&p=show&path='+strReplace("/", "_-_", (that.uploadedImages[that.selectedImageName].path).substr(7));
        }
        switch(type)
        {
            case 'facebook':
                var appID = $('[property="fb:app_id"]').attr('content');
                if(!empty(appID))
                {
                    var url = 'https://www.facebook.com/v2.9/dialog/share?app_id=' + appID + '&href=' + escape(buildURL());
                    popup(url, 'facebook', {width: 600, height: 350});                    
                }
                else alert("Can't find the fb:app_id !!");
                break;
            case 'twitter':
                var url = 'https://twitter.com/intent/tweet?text=' + escape(buildURL());
                popup(url, 'twitter', {width: 600, height: 350});
                break;
            case 'google':
                var url = 'https://plus.google.com/share?url=' + escape(buildURL());
                popup(url, 'google', {width: 600, height: 350});
                break;
            case 'linkedin':
               var url = 'https://www.linkedin.com/cws/share?url=' + escape(buildURL());
                popup(url, 'linkedin', {width: 600, height: 350});
                break;
            case 'pinterest':
                var url = 'http://pinterest.com/pin/create/button/?url=' + escape(buildURL());
                popup(url, 'pinterest', {width: 600, height: 350});
                break;
            case 'url':
                return buildURL();
                break;
        }
    };
    
    this.sendMail = function(photo, form, callBack)
    {
        waiting();
        var that = this;
        this.makePhoto(photo, null, function()
        {
            
            var image = document.getElementById("canvas_" + config.id).toDataURL();
            that.upload({
                    file: image,
                    dir: photo.name,
                    name: strReplace(" ", "_", photo.text)+(photo.append || 0).toString()+'.png',
                    email: form,
                }, function(res)
                {
                    waiting(true);
                    callBack(res);
                }
            );
        });
        
    };
    
    var photo = null;
    var config = {};
    function start(nativePhoto)
    {          
        config = {
            id: that.prop('id') || ('id_' + ++photoEditorIndexs),
            src: photo.prop('src'),
            width: nativePhoto.naturalWidth,
            oWidth: nativePhoto.naturalWidth,
            height: nativePhoto.naturalHeight,
            oHeight: nativePhoto.naturalHeight
        };
        var $widthRatio = settings.width / config.width;
        var $heightRatio = settings.height / config.height;
        config.$ratio = Math.min($widthRatio, $heightRatio);

        config.width = config.width * config.$ratio;
        config.height = config.height * config.$ratio;

        if(config.width >= config.height) photo.css('width', config.width);
        else photo.css('height', config.height);

        photo.removeClass('hidden').show();

        $('<div class="photo-preview-text"></div>').appendTo(that).prop('load', function()
        {
            //
            var text = $(this).css({top: (options.top || 10) + 'px',left: (options.left || 10) + 'px'});
            if(!options.view)
            {
                text.addClass('moving').draggable({
                    containment: "parent",
                    stop: function(event, ui)
                    {
                        if(options.onStop) options.onStop(ui, event);
                    },
                    start: function(event, ui)
                    {
                        if(options.onStart) options.onStart(ui, event);
                    },
                });
            }
            if(options.onLoad) options.onLoad(me, config);
        });

    }
    
    function checkLoaded()
    {
        photo = that.find('img.preview:eq(0)');        
        photo.each(function() 
        {    
            if(this.complete && this.naturalWidth) start(this);
            else window.setTimeout(function()
            {
                checkLoaded();
            }, 200);
        });
    }
    
    $(document).ready(function()
    {
         checkLoaded();
    });
        
    
 
   
    return this;
};

function waiting(done)
{
    $(".btn,.form-control").prop('disabled', !done);
    $("#loadingForm")[done ? 'hide' : 'show']();
}