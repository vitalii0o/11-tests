@extends('layouts.main')

@section('content')

    <div class="container">

        <h2 class="content-title">Add a new lot</h2>

        <div class="row">
            <div class="col-sm-12">
                <form id="addform" action="{{route('store_lot')}}" method="post">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="title">Name</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Enter name" value="{{ old('title') }}">
                    </div>
                    <div class="form-group">
                        <label for="short_name">Short Name</label>
                        <input type="text" class="form-control" name="short_name" id="short_name" value="{{ old('short_name') }}" placeholder="Enter short name">
                    </div>
                    <div class="form-group">
                        <label for="logo_url">Image URI</label>
                        <input type="text" class="form-control" name="logo_url" id="logo_url" value="{{ old('logo_url') }}" placeholder="Enter URI">
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="text" class="form-control" name="price" id="price" value="{{ old('price') }}" placeholder="Enter price">
                    </div>

                    <input class="btn btn-success" type="submit" value="Save">
                </form>
            </div>
        </div>
    </div>

@stop
