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
                        <li class="breadcrumb-item active">Albums</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Albums</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped projects">
                    <thead>
                    <tr>
                        <th style="width: 1%">
                            #
                        </th>
                        <th style="width: 20%">
                            Album Image
                        </th>
                        <th style="width: 30%">
                            Album Name
                        </th>
                        <th>
                            Created By
                        </th>
                        <th style="width: 20%">
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($albums as $album)
                        <tr>
                            <td>
                                #
                            </td>
                            <td>
                                <ul class="list-inline">
                                    <li class="list-inline-item">
                                        <img alt="Avatar" class="table-avatar" src="{{$album['cover_image']}}">
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <a>
                                    {{$album['name']}}
                                </a>
                                <br/>
                                <small>
                                    Created {{date('Y-m-d', strtotime($album['created_at']))}}
                                </small>
                            </td>
                            <td class="project-state">
                                <span>{{$album['created_by_name']}}</span>
                            </td>
                            <td class="project-actions text-right">
                                <a class="btn btn-primary btn-sm" href="{{url('/users/album').'/'.$album['id']}}">
                                    <i class="fas fa-folder">
                                    </i>
                                    View Friends
                                </a>
                                <a class="btn btn-primary btn-sm" href="{{url('/posts/album').'/'.$album['id']}}">
                                    <i class="fas fa-folder">
                                    </i>
                                    View Posts
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->

@endsection
