@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="card card-solid">
            <div class="card-body pb-0">
                <div class="row">
                    @foreach($users as $user)
                        <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
                        <div class="card bg-light d-flex flex-fill">
                            <div class="card-header text-muted border-bottom-0">
                                {{ucfirst($user->status)}}
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-7">
                                        <h2 class="lead"><b>{{$user->name}}</b></h2>
                                        <ul class="ml-4 mb-0 fa-ul text-muted">
                                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Email:
                                                {{$user->email}}</li>
                                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> DOB #:
                                                {{$user->dob}}</li>
                                        </ul>
                                    </div>
                                    <div class="col-5 text-center">
                                        <img src="{{$user->photo}}" alt="user-avatar" class="img-circle img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-right">
                                    <a href="{{url('/albums/user').'/'.$user->id}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-user"></i> View Albums
                                    </a>
                                    <a href="{{url('/posts/user').'/'.$user->id}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-user"></i> View Posts
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- /.card-body -->
            <!-- /.card-footer -->
        </div>
    </section>
    <!-- /.content -->

@endsection
