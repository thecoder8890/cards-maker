<?php
function show()
{
    $appendingLimit = 10;
    $fonts = include 'google-fonts.php';
    $fileExists = false;
    $path = $GLOBALS['config']['dir'].'/';
    $photo = (object) [
        'name' => $_GET['photo'],
        'path' => $path.$_GET['photo'],
        'text' => 'Your text here',
        'width' => '100',
        'left' => '10',
        'top' => '10',
        'textAlign' => 'left',
        'textColor' => '#ffffff',
        'textLength' => '100',
        'borderColor' => '#000000',
        'font' => $fonts[0],
        'fontSize' => 15,
        'borderSize' => 1,
        'imgWidth' => 100,
        'imgHeight' => 100,
        'append' => 0,
    ];
    
    if(!empty($_POST) && isAdmin)
    {
        if(!demoMode)
        {
            foreach($photo as $k=>$v)
            {
                if(isset($_POST[$k]))
                {
                    $photo->{$k} = $_POST[$k];
                }
            }
            $status = @file_put_contents($path.$_GET['photo'].'.json', json_encode($photo));
            if($status)
            {
                ?>
                <div class="alert alert-success">
                    <i class="fa fa-check"></i>
                    Data Saved success
                </div>
                <?php
            }
            $fileExists = true;
        }
        else
        {
            showDemoMode();
        }
    }
    elseif(file_exists($path.$_GET['photo'].'.json'))
    {
        $fileExists = true;
        $photoData = file_get_contents($path.$_GET['photo'].'.json');
        $photoData = json_decode($photoData);
        
        foreach($photoData as $k=>$v)
        {
            $photo->{$k} = $v;
        }
    }
    include 'templates/editor.php';
    ?>        
<script type="text/javascript">
    var imgConfg = <?php echo json_encode($photo); ?>;
    function loads(editor)
    {
            $("#photoText").keyup(function()
            {
                editor.modifyText(this.value);
                imgConfg.text = this.value;
            }).keyup();

            $("#textFont").change(function()
            {
                var font = $(this).val();
                imgConfg.font = editor.modifyFont(font);
            }).change();
            $("#photoTextWidth").change(function()
            {
                var nm = parseFloat($(this).val());
                if(nm && nm > 0)
                {
                    imgConfg.width = editor.modifyWidth(nm);
                    $(this).val(imgConfg.width);
                }
            }).keyup(function()
            {
                $(this).change();
            }).click(function()
            {
                $(this).change();
            }).change();
            $("#fontSize").blur(function()
            {
                var nm = parseFloat($(this).val());
                if(nm && nm > 0)
                {
                    imgConfg.fontSize = editor.modifyFontSize(nm);
                    $(this).val(imgConfg.fontSize);
                }
            });
            editor.modifyFontSize(parseFloat(imgConfg.fontSize));
            
            $("#borderSize").change(function()
            {
                var nm = parseFloat($(this).val());
                if(nm && nm > 0)
                {
                    imgConfg.borderSize = editor.modifyBorder(imgConfg.borderColor, nm);
                    $(this).val(imgConfg.borderSize);
                }
            }).keyup(function()
            {
                $(this).change();
            }).click(function()
            {
                $(this).change();
            }).change();

            $("#textLength").blur(function()
            {
                var nm = parseFloat($(this).val());
                if(nm && nm > 0)
                {
                    var v = $("#photoText").val();
                    v = v.substr(0, nm);
                    $("#photoText").val(v).keyup();
                    imgConfg.textLength = nm;
                    $("#photoText").prop('maxlength', imgConfg.textLength).attr('maxlength', imgConfg.textLength);
                }
            });

            function textAppending(that)
            {
                var value = $(that).attr('target');
                if(!imgConfg.append) imgConfg.append = 0;
                imgConfg.append += parseInt(value);
                
                if(imgConfg.append <= 0)
                {
                    imgConfg.append = 0;
                    $("#append button:eq(0)").prop('disabled', true);
                }
                else $("#append button:eq(0)").prop('disabled', false);
                
               
                if(imgConfg.append >= imgConfg.width - <?=$appendingLimit?>)
                {
                    imgConfg.append = imgConfg.width - <?=$appendingLimit?>;
                    $("#append button:eq(1)").prop('disabled', true);
                }
                else $("#append button:eq(1)").prop('disabled', false);
                editor.modifyAppend(imgConfg.append);
                
                $("#append input").val(value);
            }
            
            var selectedThat = null;
            window.setInterval(function()
            {
               if(selectedThat) textAppending(selectedThat);
            }, 50);
            
            $("#append button").mousedown(function()
            {
                selectedThat = this;
            }).mouseup(function()
            {
                selectedThat = null;
            })
            .on('touchstart', function(){
                selectedThat = this;
            })
            .on('touchend', function(){
                selectedThat = null;
            });
            editor.modifyAppend(parseInt(imgConfg.append));

            if(!imgConfg.textAlign) imgConfg.textAlign = 'left';
            editor.modifyAlign(imgConfg.textAlign);
            $("#textAlign [target='" + imgConfg.textAlign + "']").addClass('active');

            $("#textColor").spectrum({
                showButtons: false,
                color: imgConfg.textColor,
                change: function(color)
                {
                    imgConfg.textColor = color.toHexString();
                    editor.modifyColor(imgConfg.textColor);
                    $('[name="textColor"]').val(color.toHexString());
                }
            });
            
            editor.modifyColor(imgConfg.textColor);
            $("#borderColor").spectrum({
                showButtons: false,
                color: imgConfg.borderColor,
                change: function(color)
                {
                    imgConfg.borderColor = color.toHexString();
                    editor.modifyBorder(imgConfg.borderColor, imgConfg.borderSize);
                    $('[name="borderColor"]').val(color.toHexString());
                }
            });
            editor.modifyBorder(imgConfg.borderColor, imgConfg.borderSize);
        }
    var editor = $("#preview").photoPreview({
        view: <?php echo isAdmin && $_GET['p'] == 'edit' ? 'false' : 'true'?>,
        onStop: function(ui)
        {
            imgConfg.left = ui.position.left;
            imgConfg.top = ui.position.top;
            $("#leftPos").val(imgConfg.left);
            $("#topPos").val(imgConfg.top);
        },
        onLoad: function(editor, options)
        {
            imgConfg.imgWidth = options.width;
            imgConfg.imgHeight = options.height;
            $("#imgHeight").val(imgConfg.imgHeight);
            $("#imgWidth").val(imgConfg.imgWidth);
            loads(editor);
        },
        top: imgConfg.top,
        left: imgConfg.left,
    });
    
    $("#download").click(function()
    {
        editor.download(imgConfg);
    });
    
    $("#share").click(function()
    {
        editor.share(imgConfg);
        $('#url-val').hide();
    });
    
    $("#send").click(function()
    {
        $("#sendTo").modal('show');
    });
    
    $("#facebook-btn").click(function()
    {
        editor.shareNow(imgConfg, 'facebook');
    });
    
    $("#twitter-btn").click(function()
    {
        editor.shareNow(imgConfg, 'twitter');
    });
    
    $("#google-btn").click(function()
    {
        editor.shareNow(imgConfg, 'google');
    });
    
    $("#linkedin-btn").click(function()
    {
        editor.shareNow(imgConfg, 'linkedin');
    });
    
    $("#pinterest-btn").click(function()
    {
        editor.shareNow(imgConfg, 'pinterest');
    });
    $("#url-btn").click(function()
    {
        $('#url-val').val(editor.shareNow(imgConfg, 'url')).slideDown().select();
    });
    
    $("#sendTo form").submit(function()
    {
        var form = {};
        $(this).find('input').each(function()
        {
            form[$(this).attr('name')] = $(this).val();
        });

        editor.sendMail(imgConfg, form, function(res)
        {
            alert(res.emailStatus ? 'Mail sent successfully' : "Can't sending the mail !");
            if(res.emailStatus) $("#sendTo").modal('hide');
        });
        return false;
    });
</script>
    <?php
}

function upload()
{
    if(!empty($_FILES['images']) && !demoMode)
    {        
        $path = $GLOBALS['config']['dir'].'/';
        function theName($name)
        {
            $ext = getExt($name);
            $newName = rand(0, 99).'_'.uniqid();
            if(file_exists($GLOBALS['path'].$newName.'.'.$ext))
            {
                $name = theName($name);
            }
            else $name = $newName.'.'.$ext;
            
            return $name;
        }
        
        
        foreach($_FILES['images']['name'] as $k=>$name)
        {
            $name = theName($name);
            @move_uploaded_file($_FILES['images']['tmp_name'][$k], $path.$name);
        }
    }
    elseif(demoMode) showDemoMode ();
    include_once 'templates/upload.php';
    ?>        
    <script type="text/javascript">
        var formData = new FormData();        
        var theFiles = {};
        var index = 0;
        var temp = $("#fileShow").html();

        function addFile(file)
        {            
            formData.append("images[]", file);
            theFiles[index] = file;
            var old = ['{i}', '{name}', '{data}'];
            var newR = [index, file.name];
            readData(file, function(result)
            {
                newR.push(result);
                $("#files").append(strReplace(old, newR, temp));
            });
            index++;
            isUploadEnabled();
        }
        
        function reset()
        {
            formData = new FormData();
            $("#files").find('div').remove();
            theFiles = {};
            isUploadEnabled();
        }
        
        function startEvent()
        {            
            $('[name="theFile"]').change(function()
            {                
                $.each(this.files, function(i, file)
                {                    
                    if(file.name && file.type)
                    {
                        addFile(file);
                    }
                });                
                $(this).val('');   
//                this.files = [];
            });
        }
           
        function removeImage(i)
        {            
            formData = new FormData();            
            delete theFiles[i];
            $('#imgs_'+i).remove();
            
            $.each(theFiles, function(idx, file)
            {
                formData.append("images[]", file);
            });
            isUploadEnabled();
        }
        
        function isUploadEnabled()
        {
            $("#upload").prop('disabled', len(theFiles) == 0);
        }
        
        
        
        function upload()
        {
            if(len(theFiles) > 0)
            {
                $("#progress-wrp").removeClass('hidden');
                $.ajax({
                    type: "POST",
                    url: "?p=new",
                    xhr: function () {
                        var myXhr = $.ajaxSettings.xhr();
                        if (myXhr.upload) {
                            myXhr.upload.addEventListener('progress', progressHandling, false);
                        }
                        return myXhr;
                    },
                    success: function (data) 
                    {
//                        reset();
//                        $("#progress-wrp").addClass('hidden');
                        location.reload();
                    },
                    error: function (error) 
                    {
                        
                    },
                    async: true,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    timeout: 200000
                });
            }
        }
        
        function progressHandling(event)
        {
            var percent = 0;
            var position = event.loaded || event.position;
            var total = event.total;
            var progress_bar_id = "#progress-wrp";
            if (event.lengthComputable) {
                percent = Math.ceil(position / total * 100);
            }
            // update progressbars classes so it fits your code
            $(progress_bar_id + " .progress-bar").css("width", +percent + "%");
            $(progress_bar_id + " .status").text(percent + "%");
        }
        
        
        startEvent();

    </script>
    <?php
}


function savePhoto()
{
    if(!empty($_FILES['file']) && !empty($_POST['name']))
    {
        $names = getExt($_POST['name'], true);
        if($names->ext != 'png') exit;
        $file = '__temp/';
        if(!empty($_POST['dir'])) 
        {
            $file .= $_POST['dir'].'/';
            @mkdir($file);
        }        
        $file .= $names->name.'.png';
        
        
        
        
        $data = array('path' => $file, 'name' => $_POST['name']);
        $data['status'] = @move_uploaded_file($_FILES['file']['tmp_name'], $file);
        if(!empty($_POST['email']))
        {
            $form = (object) $_POST['email'];
            include_once 'mailer.php';
            $isHTML = in_array($GLOBALS['config']['cardsMailFormat'], ['image', 'both']);
            $mail = $isHTML ? 'photo' :'nophoto';
            $bodytext = @file_get_contents('templates/mail_'.$mail.'.php');
            $url = substr($file, 7);
            $url = str_replace("/", "_-_", $url);
            $bodytext = str_replace([
                '{imgPath}',
                '{name}',
                '{senderName}',
            ], 
            [
                sitePath('?do=share&p=show&path='.$url),
                $form->name,
                $form->yourName,
            ], $bodytext);
            $email = new PHPMailer();            
            $email->From      = !empty($GLOBALS['config']['senderMail']) ? $GLOBALS['config']['senderMail'] : 'no-replay@'.serverName();
            $email->FromName  = $form->yourName;
            $email->Subject   = $form->title;
            $email->Body      = $bodytext;
            $email->AddAddress($form->email);
            if(in_array($GLOBALS['config']['cardsMailFormat'], ['attachment', 'both'])) $email->AddAttachment($file, $_POST['name']);
            if($isHTML) $email->isHTML();
            $data['emailStatus'] = $email->Send();
        }
        echoJson($data);
    }
    echoJson(['error' => true]);
}

function view()
{
    $orginalName = explode("/", $_GET['path']);
    $orginalName = !empty($orginalName[1]) ? $orginalName[0] : null;
    include 'templates/view.php';
}