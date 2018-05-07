@if (count($errors) > 0)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <p>There were some problems with your input.</p>
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li class="d-flex mb-2"><i class="material-icons">error</i> {{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif

@if (session('message'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('message') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif