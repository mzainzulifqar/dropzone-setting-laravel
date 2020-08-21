<!-- css -->
<link href="{{ asset('backend/assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" 
type="text/css" />

<!-- Plugins js -->
<script src="{{ asset('backend/assets/libs/dropzone/min/dropzone.min.js') }}"></script>

<!-- Html Code -->
<form action="" class="dropzone" method="post" enctype="multipart/form-data">
    {!! csrf_field() !!}
</form>


<!-- Controller Code -->
public function store(Request $request, MessageBag $message_bag)
{
	if ($files = $request->file('files'))
	{
		foreach ($request->files as $file)
		{
			for ($i = 0; $i < count($file); $i++)
			{
				$name = $file[$i][$i]->getClientOriginalExtension();
				$realName = basename($file[$i][$i]->getClientOriginalName(), '.'.$file[$i][$i]->getClientOriginalExtension()) . uniqid() . 'media' . '.' . $name;
				$file[$i][$i]->move(public_path('clients/gallery'), $realName);
				$media[] = ['media' => $realName, 'ext' => $name];
			}
		}
		\DB::table('media')->insert($media);
	}

	return json_encode(array_column($media, 'media'));
}


<!-- js handler -->
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
Dropzone.autoDiscover = false;
// imageDataArray variable to set value in crud form
var imageDataArray = new Array;
// fileList variable to store current files index and name
var fileList = new Array;
var i = 0;
$(function(){
    uploader = new Dropzone(".dropzone",{
        url: "media",
        paramName : "files[]",
        uploadMultiple :true,
        acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf,.json",
        addRemoveLinks: true,
        forceFallback: false,
        maxFilesize: 10, // Set the maximum file size to 256 MB
        parallelUploads: 1,
    });//end drop zone
    uploader.on("success", function(file,response) {
        imageDataArray.push(response)
        fileList[i] = {
            "serverFileName": response,
            "fileName": file.name,
            "fileId": i
        };
   
        i += 1;
        $('#item_images').val(imageDataArray);
    });
    uploader.on("removedfile", function(file) {
        var rmvFile = "";
        for (var f = 0; f < fileList.length; f++) {
            if (fileList[f].fileName == file.name) {
                // remove file from original array by database image name
                imageDataArray.splice(imageDataArray.indexOf(fileList[f].serverFileName), 1);
                $('#item_images').val(imageDataArray);
                // get removed database file name
                rmvFile = fileList[f].serverFileName;
                // get request to remove the uploaded file from server
                
                $.get( "media/" +  rmvFile  + '/edit' )
                  .done(function( data ) {
                    //console.log(data)
                  });
                // reset imageDataArray variable to set value in crud form
                
                console.log(imageDataArray)
            }
        }
        
    });
});
</script>