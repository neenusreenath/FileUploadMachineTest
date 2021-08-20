@extends('layout.master')
@section('content')
<style>
    .comment-label{
        float: left;
        font-size: small;
    }
</style>
<div class="header bg-primary pb-6">
    <div class="container-fluid">
      <div class="header-body">
        <div class="row align-items-center py-4">
          <div class="col-lg-6 col-7">
            <h6 class="h2 text-white d-inline-block mb-0">Upload File</h6>
          </div>
        </div>
      </div>
    </div>
</div>
<div class="div2">
    <div class="card">
        <div class="card shadow">
            <div class="card-body profile-detail">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active form-input-color" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <h2 class="main-head">Upload File</h2>
                        <form role="form" method="post" id="uploadForm" name="uploadForm" enctype=multipart/form-data> 
                        @csrf
                        <div id="error" style='color:red;font-size: 0.75rem'></div>
                        <div id="success" style='color:blue;font-size: 0.75rem'></div>
                            <div class="row">
                                <div class="col-3">
                                    <span>
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Upload a CSV file</label>
                                            <input class="form-control" type="file" id="file" name="file" >
                                        </div>
                                    </span>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-3">
                                    <span>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary" id="edit-upload">Upload File</button>
                                        </div>
                                    </span>
                                </div>
                            </div> 
                        <form>
                    </div> 
                </div>
            </div>
        </div>     
    </div>   
</div>

@endsection

@push('scripts')


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script>

jQuery.validator.addMethod("extension", function (value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "gif";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, "Please enter a file with a valid extension. Allowed file formats are pdf,doc,jpg and png");

$("#uploadForm").validate({
    submitHandler: function(form, event) {
        event.preventDefault();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
  
    var fd = new FormData($("#uploadForm")[0]);
    
    $.ajax({
        method: 'post',
        data: fd,
        url: "{{url('upload-result')}}",
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(response) {
            $("#success").html("Your file is successfully imported");
            setTimeout(function(){
                $("#success").hide();
                $("#success").html("");
            },3000);
        },
        error: function(data) {
            // console.log(data.responseJSON.data.length);
            // var i;
            // for(i=0;i<data.responseJSON.data.length;i++)
            // {
                // $("#error").append("<div style='color:red;font-size: 0.75rem'>"+data.responseJSON.data[i]+"</div>")
            // }
            // $("#div")
            $("#error").html("Some error with your CSV file data");
            setTimeout(function(){
                $("#error").hide();
                $("#error").html("");
            },3000);
            },
        });
    }
});

</script>
@endpush
