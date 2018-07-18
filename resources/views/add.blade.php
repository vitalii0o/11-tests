@extends('layouts.main')

@section('content')

    <div class="container">

        <h2 class="content-title">Add new lot</h2>

        <div class="row">
            <div class="col-sm-12">
                <form id="addform" action="{{route('store_currency')}}" method="post">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="title">Name</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Enter name" value="{{ old('title') }}">
                        @if(isset($errors))
                            <small id="titleHelp" class="form-text text-error">{{ $errors->first('title') }}</small>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="short_name">Short Name</label>
                        <input type="text" class="form-control" name="short_name" id="short_name" value="{{ old('short_name') }}" placeholder="Enter short name">
                        @if(isset($errors))
                            <small id="short_nameHelp" class="form-text text-error">{{ $errors->first('short_name') }}</small>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="logo_url">Image URI</label>
                        <input type="text" class="form-control" name="logo_url" id="logo_url" value="{{ old('logo_url') }}" placeholder="Enter URI">
                        @if(isset($errors))
                            <small id="logo_urlHelp" class="form-text text-error">{{ $errors->first('logo_url') }}</small>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="text" class="form-control" name="price" id="price" value="{{ old('price') }}" placeholder="Enter price">
                        @if(isset($errors))
                            <small id="priceHelp" class="form-text text-error">{{ $errors->first('price') }}</small>
                        @endif
                    </div>

                    <input class="btn btn-success" type="submit" value="Save">
                </form>
            </div>
        </div>
    </div>

@stop
