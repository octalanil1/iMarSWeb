<script type="text/javascript">
    $(document).ready(function () 
    {   $.LoadingOverlay("hide");
        $( '#addContentForm' ).on( 'submit', function(e) 
        {
            e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
          
              var title = $('#title').val();
            var subject = $('#subject').val();
            var description = CKEDITOR.instances['description'].getData();
            $.ajax({
                dataType: 'json',
            data: {title:title,subject:subject,description:description}, 
            type: "POST",
            url: '{{ URL::to('/admin/add-email-templates-post') }}',
        }).done(function( data ) 
        {  error_remove (); if(data.success==false)
            {
                $.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                });

          }else{
            search();
            if(data.class == 'success'){showMsg(data.message, "success");}
            $("#myModal").modal('hide');
            
	            return false;
          }
            
                         
        });

    });
});
</script>

    {!! Form::open(array('url' => '/admin/add-email-templated-post', 'method' => 'post','name'=>'addContentForm','files'=>true,'novalidate' => 'novalidate','id' => 'addContentForm')) !!}
    <div class="col-sm-12 p-r-30">
        <div class="panel form-horizontal panel-transparent">
            <div class="panel-body">
                <div class="row">
                <div class="alert alert-danger" style="display:none"></div>
                
                <div class="col-12 col-sm-12 col-md-12">
                            <div class="row form-group">
                                <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Title<span class="red-star">*</span></label>
                                <div class="col-lg-10 col-sm-6 col-xs-12">
                                
                                {!! Form::text('title',null, ['class' => 'form-control','placeholder' => 'Title','required'=>'required','id'=>'title','onkeypress' => 'error_remove()' ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="row form-group">
                                <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Subject<span class="red-star">*</span></label>
                                <div class="col-lg-10 col-sm-6 col-xs-12">
                                
                                {!! Form::text('subject',null, ['class' => 'form-control','placeholder' => 'subject','required'=>'required','id'=>'subject','onkeypress' => 'error_remove()' ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="row form-group">
                                <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Description<span class="red-star">*</span></label>
                                <div class="col-lg-10 col-sm-6 col-xs-12">
                                    <textarea id="description" name="description" rows="10" cols="80"></textarea>
                                </div>
                            </div>
                        </div>
                    
                   
                </div>
            </div>
        </div>
    </div>
    <div class="row form-btn text-center">
        <div class="col-sm-12 p-r-30">
        <div class="col-md-12"> 
        {!! Form::submit('Save',['class' => 'btn btn-primary btn-flat subbtn', 'type' => 'submit']) !!}
        {!! Form::submit('Cancel',['class' => 'btn btn-flat subbtn','data-dismiss'=>'modal']) !!}
        </div>
    </div>
    {!! Form::close() !!}
    <script src="{{asset('/public/admin_assets/bower_components/ckeditor/ckeditor.js')}}"></script>

<script>
$(function () {
// Replace the <textarea id="editor1"> with a CKEditor
// instance, using default configuration.
CKEDITOR.replace('description')
//bootstrap WYSIHTML5 - text editor
$('.textarea').wysihtml5()
})
</script>