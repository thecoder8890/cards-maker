<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo (isAdmin && $_GET['p'] == 'edit' ? 'Edit Photo ' . $photo->name : 'Type Your name'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="pull-left photo-preview" style="margin-right: 15px" id="preview">
            <div class="mask"></div>
            <img src="<?php echo $photo->path ?>" class="preview hidden" />
        </div>
        <?php if (isAdmin && $_GET['p'] == 'edit') { ?>
            <div class="pull-left" style="width: 400px;">
                <form action="" method="post">
                    <input type="hidden" name="left" id="leftPos" value="<?php echo $photo->left; ?>" />
                    <input type="hidden" name="top" id="topPos" value="<?php echo $photo->top; ?>" />             
                    <div class="col-lg-6">
                        <label>Text:</label>
                        <input id="photoText" type="text" 
                               maxlength="<?php echo $photo->textLength; ?>"
                               name="text"
                               class="form-control" placeholder="Your text here" value="<?php echo $photo->text; ?>" />              
                    </div>
                    <div class="col-lg-3">
                        <label>Text:</label><br />
                        <input type='text' id="textColor" />
                        <input type='hidden' name="textColor" value="<?php echo $photo->textColor; ?>" />
                    </div>
                    <div class="col-lg-3">
                        <label>Border:</label><br />
                        <input type='text' id="borderColor" />
                        <input type='hidden' name="borderColor" value="<?php echo $photo->borderColor; ?>" />
                    </div>
                    <div class="col-lg-12"><hr /></div>
                    <div class="col-lg-5">
                        <label>Font Type:</label>

<!--                        <select class="form-control" id="textFont" name="font">
                            <?php foreach ($fonts as $font) { ?>
                                <option <?php echo $photo->font == $font ? 'selected="selected"' : ''; ?>><?php echo $font ?></option>
                            <?php } ?>
                        </select>-->
                        
                        
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <span><?php echo $photo->font?></span>
                            <input type="hidden" name="font" id="textFont" value="<?php echo $photo->font?>" />
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height: 250px;overflow: auto;width: 450px">
                            <?php foreach ($fonts as $font) { ?>
                            <li>
                                <a href="#" onclick="$('#textFont').val('<?php echo $font ?>').change().prev().html('<?php echo $font ?>');return false;">
                                    <div class="pull-right btn btn-default btn-sm" style="font-family: <?php echo $font ?>;">
                                        Example
                                    </div>
                                    <?php echo $font ?>                                    
                                </a>
                            </li>   
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <label>Font Size:</label>
                        <input id="fontSize" name="fontSize" type="number" class="form-control numaric_only text-center" placeholder="Font Size" 
                               value="<?php echo $photo->fontSize; ?>" />
                    </div>
                    <div class="col-lg-4">
                        <label>Border Size:</label>
                        <input id="borderSize" name="borderSize" type="number" class="form-control numaric_only text-center" placeholder="Border Size" 
                               value="<?php echo $photo->borderSize; ?>" />
                    </div>
                    <div class="col-lg-12"><hr /></div>
                    <div class="col-lg-6">
                        <label>Label Width:</label>
                        <input id="photoTextWidth" name="width" type="number" class="form-control numaric_only text-center" placeholder="Text Width" 
                               value="<?php echo $photo->width; ?>" />
                    </div>
                    <div class="col-lg-6">
                        <label>Text max length:</label>
                        <input id="textLength" name="textLength" type="number" class="form-control numaric_only text-center" 
                               placeholder="Text Max Length"                            
                               value="<?php echo $photo->textLength; ?>" />
                    </div>
                    <div class="col-lg-12">
                        <br />
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i>
                            Save
                        </button>
                        <?php if($fileExists) { ?>
                        <button type="button" class="btn btn-info"
                                onclick="goto('?photo=<?php echo $_GET['photo'] ?>&p=view');">
                            <i class="fa fa-eye"></i>
                            Preview
                        </button>
                        <?php } ?>
                        
                        <button type="button" class="btn btn-danger pull-right"
                                onclick="ask_b('Do you want to delete this image ?', '?photo=<?php echo $_GET['photo'] ?>&p=delete');">
                            <i class="fa fa-remove"></i>
                            Delete
                        </button>
                        <input type="hidden" name="imgWidth" id="imgWidth" value="<?php echo $photo->imgWidth; ?>" />
                        <input type="hidden" name="imgHeight" id="imgHeight" value="<?php echo $photo->imgHeight; ?>" />
                    </div>
                </form>
            </div>
        <?php } else { ?>

            <div class="pull-left" style="max-width: 400px;">
                <div class="row">
                    <div class="col-lg-7">
                        <label><?php echo $photo->text; ?>:</label><br />
                        <input id="photoText" type="text" 
                               maxlength="<?php echo $photo->textLength; ?>"
                               class="form-control" placeholder="<?php echo $photo->text; ?>" value="<?php echo $photo->text; ?>" />
                    </div>
                    <div class="col-lg-5">
                        <label>Text Align:</label><br />
                        <div class="btn-group" id="append" role="group" style="width: 100px">
                            <input type="hidden" name="append" value="<?php echo $photo->append; ?>" />
                            <button type="button" style="width: 50px" <?php echo $photo->append <= 0 ? 'disabled=""' : ''; ?> target="-1" class="btn btn-default"><i class="fa fa-angle-left"></i></button>
                            <button type="button" style="width: 50px" <?php echo $photo->append >= $photo->width - $appendingLimit ? 'disabled=""' : ''; ?> target="1" class="btn btn-default"><i class="fa fa-angle-right"></i></button>
                        </div>  
                    </div> 
                </div> 

                <input type="hidden" id="textFont" value="<?php echo $photo->font ?>" />
                <input id="fontSize" type="hidden" value="<?php echo $photo->fontSize; ?>" />
                <input id="photoTextWidth" type="hidden" value="<?php echo $photo->width; ?>" />
                <div class="col-lg-12"><br /></div>
                <div>
                    <button type="button" class="btn btn-primary" id="download">
                        <i class="fa fa-download"></i>
                        Save
                    </button>
                    <button type="button" class="btn btn-info" id="share">
                        <i class="fa fa-share-alt"></i>
                        Share
                    </button>
                    <button type="button" class="btn btn-warning" id="send">
                        <i class="fa fa-envelope"></i>
                        Send 
                    </button>
                </div>
                <?php if(isAdmin) { ?>
                <div>
                    <div class="clearfix"><br></div>
                        <button type="button" class="btn btn-success"
                                 onclick="goto('?photo=<?php echo $_GET['photo'] ?>&p=edit');">
                            <i class="fa fa-pencil"></i>
                            Edit
                        </button>
                    
                        <button type="button" class="btn btn-danger "
                                 onclick="ask_b('Do you want to delete this image ?', '?photo=<?php echo $_GET['photo'] ?>&p=delete');">
                            <i class="fa fa-remove"></i>
                            Delete
                        </button>
                </div>
                <?php } ?>
                
                <div style="width: 300px;">                    
                    <?php showAds2()?>
                </div>
            </div>
        
            <div class="modal fade" id="sharing" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Share</h4>
                        </div>
                        <div class="modal-body text-center">
                            <button type="button" style="width: 80px" title="Share on Facebook" id="facebook-btn" class="btn btn-primary">
                                <i class="fa fa-facebook fa-3x"></i>
                            </button>
                            <button type="button" style="width: 80px" title="Share on Twitter" id="twitter-btn" class="btn btn-info">
                                <i class="fa fa-twitter fa-3x"></i>
                            </button>
                            <button type="button" style="width: 80px" title="Share on Google Plus" id="google-btn" class="btn btn-danger">
                                <i class="fa fa-google-plus fa-3x"></i>
                            </button>
                            <button type="button" style="width: 80px" title="Share on Linkedin" id="linkedin-btn" class="btn btn-primary">
                                <i class="fa fa-linkedin fa-3x"></i>
                            </button>
                            <button type="button" style="width: 80px" title="Share on Pinterest" id="pinterest-btn" class="btn btn-danger">
                                <i class="fa fa-pinterest fa-3x"></i>
                            </button>
                            <button type="button" style="width: 80px" title="URL" id="url-btn" class="btn btn-default">
                                <i class="fa fa-link fa-3x"></i>
                            </button>
                            
                            <div class="form-group-lg form-group">
                                <br />
                                <input type="text" class="form-control" onclick="this.select()" id="url-val" value="" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            
            <div class="modal fade" id="sendTo" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Send to</h4>
                        </div>
                        <form>
                            <div class="modal-body">
                                    <div class="form-group">
                                        <label>Your Name</label>
                                        <input type="text" required="required" class="form-control" name="yourName" placeholder="Your Name">
                                    </div>
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" required="required" class="form-control" name="title" placeholder="Message Title">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Receiver Email</label>
                                        <input type="email" required="required" class="form-control" name="email" placeholder="Receiver Email">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Receiver Name</label>
                                        <input type="text" required="required" class="form-control" name="name" placeholder="Receiver Name">
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        <?php } ?>
    </div>
</div>